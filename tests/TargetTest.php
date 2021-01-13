<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this target code.
 */

namespace Opportus\ObjectMapper\Tests;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\TargetPoint;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\TargetInterface;
use ReflectionClass;
use ReflectionObject;

/**
 * The target test.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class TargetTest extends Test
{
    /**
     * @dataProvider provideTarget
     */
    public function testConstruct($providedTarget): void
    {
        $target = $this->createTarget($providedTarget);

        static::assertInstanceOf(TargetInterface::class, $target);
    }

    /**
     * @dataProvider provideInvalidTarget
     */
    public function testConstructException($providedTarget): void
    {
        if (
            false === \is_object($providedTarget) &&
            false === \is_string($providedTarget)
        ) {
            $this->expectException(InvalidArgumentException::class);

            $this->createTarget($providedTarget);
        }

        if (
            \is_string($providedTarget) &&
            false === \class_exists($providedTarget)
        ) {
            $this->expectException(InvalidArgumentException::class);

            $this->createTarget($providedTarget);
        }
    }

    /**
     * @dataProvider provideTarget
     */
    public function testGetFqn($providedTarget): void
    {
        $target = $this->createTarget($providedTarget);

        $targetClass = \is_object($providedTarget) ?
            \get_class($providedTarget) : $providedTarget;

        static::assertSame($targetClass, $target->getFqn());
    }

    /**
     * @dataProvider provideTarget
     */
    public function testGetClassReflection($providedTarget): void
    {
        $target = $this->createTarget($providedTarget);

        $targetClassReflection1 = $target->getClassReflection();
        $targetClassReflection2 = $target->getClassReflection();

        static::assertEquals(
            new ReflectionClass($providedTarget),
            $targetClassReflection1
        );

        static::assertEquals(
            new ReflectionClass($providedTarget),
            $targetClassReflection2
        );

        static::assertNotSame(
            $targetClassReflection1,
            $targetClassReflection2
        );
    }

    /**
     * @dataProvider provideTarget
     * @depends testGetInstance
     */
    public function testGetObjectReflection($providedTarget): void
    {
        $target = $this->createTarget($providedTarget);

        $targetObjectReflection1 = $target->getObjectReflection();
        $targetObjectReflection2 = $target->getObjectReflection();

        if ($target->getInstance()) {
            static::assertEquals(
                new ReflectionObject($providedTarget),
                $targetObjectReflection1
            );

            static::assertEquals(
                new ReflectionObject($providedTarget),
                $targetObjectReflection2
            );

            static::assertNotSame(
                $targetObjectReflection1,
                $targetObjectReflection2
            );
        } else {
            static::assertNull($target->getObjectReflection());
        }
    }

    /**
     * @dataProvider provideTarget
     */
    public function testGetInstance($providedTarget): void
    {
        $target = $this->createTarget($providedTarget);

        if (\is_string($providedTarget)) {
            static::assertNull($target->getInstance());
        } elseif (\is_object($providedTarget)) {
            static::assertSame($providedTarget, $target->getInstance());
        }
    }

    /**
     * @depends testGetFqn
     */
    public function testSetPointValueFirstInvalidArgumentException(): void
    {
        foreach ($this->provideTarget() as $providedTarget) {
            $providedTarget = $providedTarget[0];

            $target = $this->createTarget($providedTarget);

            foreach ($this->provideTargetPoint() as $point) {
                $point = $point[0];

                if ($target->getFqn() === $point->getTargetFqn()) {
                    continue;
                }

                $this->expectException(InvalidArgumentException::class);

                $target->setPointValue($point, 1);
            }
        }
    }

    /**
     * @dataProvider provideTarget
     * @depends testGetFqn
     */
    public function testSetPointValueSecondInvalidArgumentException(
        $providedTarget
    ): void {
        $target = $this->createTarget($providedTarget);

        $point = $this->getMockForAbstractClass(
            TargetPoint::class,
            [],
            '',
            false,
            true,
            true,
            ['getTargetFqn']
        );

        $point->method('getTargetFqn')->willReturn($target->getFqn());

        $this->expectException(InvalidArgumentException::class);

        $target->setPointValue($point, 1);
    }

    /**
     * @dataProvider provideTarget
     * @depends testSetPointValueFirstInvalidArgumentException
     * @depends testSetPointValueSecondInvalidArgumentException
     * @depends testGetFqn
     * @depends testGetInstance
     * @depends testGetObjectReflection
     */
    public function testOperate($providedTarget): void
    {
        $target = $this->createTarget($providedTarget);

        foreach ($this->provideTargetPoint() as $point) {
            $point = $point[0];

            if ($target->getFqn() !== $point->getTargetFqn()) {
                continue;
            }

            $target->setPointValue($point, 1);
        }

        $target->operate();

        if (\is_string($providedTarget)) {
            static::assertIsObject($target->getInstance());
            static::assertIsObject($target->getObjectReflection());
        }

        $source = $this->createSource($target->getInstance());
        $setPoints = [];

        foreach ($this->provideSourcePoint() as $point) {
            $point = $point[0];

            if ($source->getFqn() !== $point->getSourceFqn()) {
                continue;
            }

            static::assertSame(1, $source->getPointValue($point));

            $setPoints[] = $point;
        }

        $propertyReflections = $source->getObjectReflection()
            ->getProperties();

        foreach ($propertyReflections as $propertyReflection) {
            foreach ($setPoints as $point) {
                $propertyName = $point->getName();

                if (0 === \strpos($propertyName, 'get')) {
                    $propertyName = \lcfirst(\substr($propertyName, 3));
                } elseif (0 === \strpos($propertyName, 'is')) {
                    $propertyName = \lcfirst(\substr($propertyName, 2));
                }

                if ($propertyName === $propertyReflection->getName()) {
                    continue 2;
                }
            }

            $propertyReflection->setAccessible(true);

            static::assertSame(
                0,
                $propertyReflection->getValue($source->getInstance())
            );
        }
    }

    /**
     * @dataProvider provideTarget
     * @depends testSetPointValueFirstInvalidArgumentException
     * @depends testSetPointValueSecondInvalidArgumentException
     * @depends testGetFqn
     */
    public function testOperateException($providedTarget): void
    {
        if (\is_object($providedTarget)) {
            $originalTargetSnapshot = \serialize($providedTarget);
        }

        $target = $this->createTarget($providedTarget);

        foreach ($this->provideTargetPoint() as $point) {
            $point = $point[0];

            if ($target->getFqn() !== $point->getTargetFqn()) {
                continue;
            }

            $target->setPointValue($point, 1);
        }

        $targetReflection = new ReflectionClass($target);
        $pointValuesProperty = $targetReflection->getProperty('pointValues');
        $pointValuesProperty->setAccessible(true);
        $pointValues = $pointValuesProperty->getValue($target);
        $pointValues['static_method_parameters']['non_existing_method'] = [];
        $pointValuesProperty->setValue($target, $pointValues);

        if (\is_object($providedTarget)) {
            try {
                $target->operate();
            } catch (InvalidOperationException $exception) {
                static::assertSame(
                    $originalTargetSnapshot,
                    \serialize($target->getInstance())
                );
            }

            $target->operate();
        } else {
            $this->expectException(InvalidOperationException::class);

            $target->operate();
        }
    }

    private function createTarget($target): Target
    {
        return new Target($target);
    }

    private function createSource(object $source): SourceInterface
    {
        return new Source($source);
    }
}
