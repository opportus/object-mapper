<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\MethodDynamicSourcePoint;
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyDynamicSourcePoint;
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\SourcePoint;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\SourceInterface;
use ReflectionClass;
use ReflectionObject;

/**
 * The source test.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class SourceTest extends Test
{
    /**
     * @dataProvider provideSource
     */
    public function testConstruct(object $providedSource): void
    {
        $source = $this->createSource($providedSource);

        static::assertInstanceOf(SourceInterface::class, $source);
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetFqn(object $providedSource): void
    {
        $source = $this->createSource($providedSource);

        static::assertSame(\get_class($providedSource), $source->getFqn());
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetClassReflection(object $providedSource): void
    {
        $source = $this->createSource($providedSource);

        $sourceClassReflection1 = $source->getClassReflection();
        $sourceClassReflection2 = $source->getClassReflection();

        static::assertEquals(
            new ReflectionClass($providedSource),
            $sourceClassReflection1
        );

        static::assertEquals(
            new ReflectionClass($providedSource),
            $sourceClassReflection2
        );

        static::assertNotSame(
            $sourceClassReflection1,
            $sourceClassReflection2
        );
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetObjectReflection(object $providedSource): void
    {
        $source = $this->createSource($providedSource);

        $sourceObjectReflection1 = $source->getObjectReflection();
        $sourceObjectReflection2 = $source->getObjectReflection();

        static::assertEquals(
            new ReflectionObject($providedSource),
            $sourceObjectReflection1
        );

        static::assertEquals(
            new ReflectionObject($providedSource),
            $sourceObjectReflection2
        );

        static::assertNotSame(
            $sourceObjectReflection1,
            $sourceObjectReflection2
        );
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetInstance(object $providedSource): void
    {
        $source = $this->createSource($providedSource);

        static::assertSame($providedSource, $source->getInstance());
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetPointValue(object $providedSource): void
    {
        $source = $this->createSource($providedSource);

        foreach ($this->provideSourcePoint() as $point) {
            $point = $point[0];

            if ($source->getFqn() !== $point->getSourceFqn()) {
                continue;
            }

            $classReflection = new ReflectionClass($providedSource);

            if ($point instanceof PropertyStaticSourcePoint) {
                $propertyReflection = $classReflection
                    ->getProperty($point->getName());

                $propertyReflection->setAccessible(true);

                $expectedValue = $propertyReflection->getValue($providedSource);
            } elseif ($point instanceof MethodStaticSourcePoint) {
                $methodReflection = $classReflection
                    ->getMethod($point->getName());

                $methodReflection->setAccessible(true);

                $expectedValue = $methodReflection->invoke($providedSource);
            } elseif ($point instanceof PropertyDynamicSourcePoint) {
                $expectedValue = $providedSource->{$point->getName()};
            } elseif ($point instanceof MethodDynamicSourcePoint) {
                $expectedValue = $providedSource->{$point->getName()}();
            } else {
                throw new TestInvalidArgumentException(1, __METHOD__, '');
            }

            static::assertEquals(
                $expectedValue,
                $source->getPointValue($point)
            );
        }
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetPointValueFirstInvalidArgumentException(
        object $providedSource
    ): void {
        $source = $this->createSource($providedSource);

        foreach ($this->provideSourcePoint() as $point) {
            $point = $point[0];

            if ($source->getFqn() === $point->getSourceFqn()) {
                continue;
            }

            $this->expectException(InvalidArgumentException::class);

            $source->getPointValue($point);
        }
    }

    /**
     * @dataProvider provideSource
     * @depends testGetFqn
     */
    public function testGetPointValueSecondInvalidArgumentException(
        object $providedSource
    ): void {
        $source = $this->createSource($providedSource);

        $point = $this->getMockForAbstractClass(
            SourcePoint::class,
            [],
            '',
            false,
            true,
            true,
            ['getSourceFqn']
        );

        $point->method('getSourceFqn')->willReturn($source->getFqn());

        $this->expectException(InvalidArgumentException::class);

        $source->getPointValue($point);
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetPointValueInvalidOperationException(
        object $providedSource
    ): void {
        $source = $this->createSource($providedSource);

        $point = $this->getMockBuilder(PropertyStaticSourcePoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $point->method('getSourceFqn')->willReturn($source->getFqn());

        $this->expectException(InvalidOperationException::class);

        $source->getPointValue($point);
    }

    private function createSource(object $source): Source
    {
        return new Source($source);
    }
}
