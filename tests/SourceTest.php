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
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\SourcePoint;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\TargetInterface;
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
    use TestDataProviderTrait;

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
        $source = $this->buildSource($providedSource);

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
        $source = $this->buildSource($providedSource);

        static::assertSame($providedSource, $source->getInstance());
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetPointValue(object $providedSource): void
    {
        $target = $this->buildTarget($providedSource);

        foreach ($this->provideTargetPoint() as $point) {
            $point = $point[0];

            if ($target->getFqn() !== $point->getTargetFqn()) {
                continue;
            }

            $target->setPointValue($point, 1);
        }

        $target->operate();

        $source = $this->buildSource($providedSource);

        foreach ($this->provideSourcePoint() as $point) {
            $point = $point[0];

            if ($source->getFqn() !== $point->getSourceFqn()) {
                continue;
            }

            static::assertEquals(1, $source->getPointValue($point));
        }
    }

    /**
     * @dataProvider provideSource
     */
    public function testGetPointValueFirstInvalidArgumentException(
        object $providedSource
    ): void {
        $source = $this->buildSource($providedSource);

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
        $source = $this->buildSource($providedSource);

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
        $source = $this->buildSource($providedSource);

        $point = $this->getMockBuilder(PropertyStaticSourcePoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $point->method('getSourceFqn')->willReturn($source->getFqn());

        $this->expectException(InvalidOperationException::class);

        $source->getPointValue($point);
    }

    private function buildSource(object $source): Source
    {
        return new Source($source);
    }

    private function buildTarget($target): TargetInterface
    {
        return new Target($target);
    }
}
