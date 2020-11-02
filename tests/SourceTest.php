<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\DynamicSourcePointInterface;
use Opportus\ObjectMapper\Point\MethodDynamicSourcePoint;
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyDynamicSourcePoint;
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\StaticSourcePointInterface;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\SourceInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionObject;

/**
 * The source test.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class SourceTest extends TestCase
{
    use ProviderTrait;

    /**
     * @dataProvider provideSource
     */
    public function testConstruct(object $providedSource): void
    {
        $source = $this->buildSource($providedSource);

        static::assertInstanceOf(SourceInterface::class, $source);
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetFqn(object $providedSource): void
    {
        $source = $this->buildSource($providedSource);

        static::assertSame(\get_class($providedSource), $source->getFqn());
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetClassReflection(object $providedSource): void
    {
        $source = $this->buildSource($providedSource);

        static::assertEquals(new ReflectionClass($providedSource), $source->getClassReflection());
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetObjectReflection(object $providedSource): void
    {
        $source = $this->buildSource($providedSource);

        static::assertEquals(new ReflectionObject($providedSource), $source->getObjectReflection());
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetInstance(object $providedSource): void
    {
        $source = $this->buildSource($providedSource);

        static::assertSame($providedSource, $source->getInstance());
    }

    /**
     * @dataProvider provideStaticSourcePoint
     */
    public function testHasStaticPoint(StaticSourcePointInterface $point): void
    {
        $source = $this->buildSource(new ObjectA());

        if (ObjectA::class === $point->getSourceFqn()) {
            static::assertTrue($source->hasStaticPoint($point));
        } elseif (ObjectB::class === $point->getSourceFqn()) {
            static::assertFalse($source->hasStaticPoint($point));
        } else {
            throw new InvalidArgumentException(1, __METHOD__, '');
        }

        $source = $this->buildSource(new ObjectB());

        if (ObjectB::class === $point->getSourceFqn()) {
            static::assertTrue($source->hasStaticPoint($point));
        } elseif (ObjectA::class === $point->getSourceFqn()) {
            static::assertFalse($source->hasStaticPoint($point));
        } else {
            throw new InvalidArgumentException(1, __METHOD__, '');
        }
    }

    /**
     * @dataProvider provideDynamicSourcePoint
     */
    public function testHasDynamicPoint(DynamicSourcePointInterface $point): void
    {
        $sourceInstance = new ObjectA();
        $sourceInstance->y = 1;

        $sourceReflection = new ReflectionObject($sourceInstance);

        $source = $this->buildSource($sourceInstance);

        if (
            ObjectA::class === $point->getSourceFqn() &&
            (
                $point instanceof PropertyDynamicSourcePoint &&
                $sourceReflection->hasProperty($point->getName()) ||
                $point instanceof MethodDynamicSourcePoint &&
                \is_callable([$sourceInstance, $point->getName()])
            )
        ) {
            static::assertTrue($source->hasDynamicPoint($point));
        } else {
            static::assertFalse($source->hasDynamicPoint($point));
        }

        $sourceInstance = new ObjectB();
        $sourceInstance->y = 1;

        $sourceReflection = new ReflectionObject($sourceInstance);

        $source = $this->buildSource($sourceInstance);


        if (
            ObjectB::class === $point->getSourceFqn() &&
            (
                $point instanceof PropertyDynamicSourcePoint &&
                $sourceReflection->hasProperty($point->getName()) ||
                $point instanceof MethodDynamicSourcePoint &&
                \is_callable([$sourceInstance, $point->getName()])
            )
        ) {
            static::assertTrue($source->hasDynamicPoint($point));
        } else {
            static::assertFalse($source->hasDynamicPoint($point));
        }
    }

    /**
     * @dataProvider provideSourcePoint
     */
    public function testGetPointvalue(SourcePointInterface $point): void
    {
        $sourceInstance = new ObjectA();
        $sourceInstance->f = 1;
        $sourceInstance->y = 1;

        $sourceReflection = new ReflectionObject($sourceInstance);

        $source = $this->buildSource($sourceInstance);

        if ($point instanceof PropertyStaticSourcePoint) {
            $pointReflection = $sourceReflection
                ->getProperty($point->getName());

            $pointReflection->setAccessible(true);

            if (
                $sourceReflection->getName() !== $point->getSourceFqn() ||
                false === $sourceReflection->hasProperty($point->getName())
            ) {
                $this->expectException(InvalidArgumentException::class);

                $source->getPointValue($point);

                return;
            }

            static::assertEquals(
                $pointReflection->getValue($sourceInstance),
                $source->getPointValue($point)
            );
        } elseif ($point instanceof PropertyDynamicSourcePoint) {
            $pointReflection = $sourceReflection
                ->getProperty($point->getName());

            if (
                $sourceReflection->getName() !== $point->getSourceFqn() ||
                false === $sourceReflection->hasProperty($point->getName())
            ) {
                $this->expectException(InvalidArgumentException::class);

                $source->getPointValue($point);

                return;
            }

            static::assertEquals(
                $pointReflection->getValue($sourceInstance),
                $source->getPointValue($point)
            );
        } elseif ($point instanceof MethodStaticSourcePoint) {
            $pointReflection = $sourceReflection->getMethod($point->getName());

            $pointReflection->setAccessible(true);

            if (
                $sourceReflection->getName() !== $point->getSourceFqn() ||
                false === $sourceReflection->hasMethod($point->getName())
            ) {
                $this->expectException(InvalidArgumentException::class);

                $source->getPointValue($point);

                return;
            }

            static::assertEquals(
                $pointReflection->invoke($sourceInstance),
                $source->getPointValue($point)
            );
        } elseif ($point instanceof MethodDynamicSourcePoint) {
            if (
                $sourceReflection->getName() !== $point->getSourceFqn() ||
                false === \is_callable([$sourceInstance, $point->getName()])
            ) {
                $this->expectException(InvalidArgumentException::class);

                $source->getPointValue($point);

                return;
            }

            static::assertEquals(
                $sourceInstance->{$point->getName()}(),
                $source->getPointValue($point)
            );
        } else {
            $this->expectException(InvalidOperationException::class);

            $source->getPointValue($point);
        }
    }

    private function buildSource(object $source): Source
    {
        return new Source($source);
    }
}
