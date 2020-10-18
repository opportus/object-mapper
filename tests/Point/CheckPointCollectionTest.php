<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Point;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\ImmutableCollection;
use Opportus\ObjectMapper\Point\CheckPointCollection;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * The check point collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class CheckPointCollectionTest extends TestCase
{
    /**
     * @dataProvider provideConstructArguments
     */
    public function testConstruct(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        static::assertInstanceOf(
            CheckPointCollection::class,
            $checkPointCollection
        );

        static::assertInstanceOf(
            ImmutableCollection::class,
            $checkPointCollection
        );

        static::assertInstanceOf(
            ArrayAccess::class,
            $checkPointCollection
        );

        static::assertInstanceOf(
            Countable::class,
            $checkPointCollection
        );

        static::assertInstanceOf(
            IteratorAggregate::class,
            $checkPointCollection
        );

        static::assertContainsOnlyInstancesOf(
            CheckPointInterface::class,
            $checkPointCollection
        );

        static::assertEquals(
            \count($checkPoints),
            \count($checkPointCollection)
        );

        foreach ($checkPoints as $checkPointPosition=> $checkPoint) {
            static::assertArrayHasKey(
                $checkPointPosition,
                $checkPointCollection
            );

            static::assertSame(
                $checkPoint,
                $checkPointCollection[$checkPointPosition]
            );
        }
    }

    /**
     * @dataProvider provideConstructInvalidArguments
     */
    public function testConstructException(array $checkPoints): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CheckPointCollection($checkPoints);
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testToArray(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition=> $checkPoint) {
            static::assertArrayHasKey(
                $checkPointPosition,
                $checkPointCollection->toArray()
            );

            static::assertSame(
                $checkPoint,
                $checkPointCollection->toArray()[$checkPointPosition]
            );
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testGetIterator(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        $iterator = $checkPointCollection->getIterator();

        static::assertInstanceOf(ArrayIterator::class, $iterator);
        static::assertSame(\count($checkPoints), \count($iterator));

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            static::assertArrayHasKey($checkPointPosition, $iterator);
            static::assertSame($checkPoint, $iterator[$checkPointPosition]);
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testCount(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        static::assertSame(
            \count($checkPoints),
            $checkPointCollection->count()
        );
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetExists(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            static::assertTrue(
                $checkPointCollection->offsetExists($checkPointPosition)
            );
        }

        static::assertFalse($checkPointCollection->offsetExists(4));
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetGet(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            static::assertSame(
                $checkPoint,
                $checkPointCollection->offsetGet($checkPointPosition)
            );
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetSet(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            $this->expectException(InvalidOperationException::class);

            $checkPointCollection->offsetSet($checkPointPosition, $checkPoint);
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetUnset(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            $this->expectException(InvalidOperationException::class);

            $checkPointCollection->offsetUnset($checkPointPosition);
        }
    }

    public function provideConstructArguments(): array
    {
        $checkPoints = [];
        for ($i = 0; $i < 3; $i++) {
            $checkPoint = $this->getMockBuilder(CheckPointInterface::class)
                ->getMock()
            ;

            $checkPoints[$i] = $checkPoint;
        }

        return [
            [
                $checkPoints
            ]
        ];
    }

    public function provideConstructInvalidArguments(): array
    {
        return [
            [
                [
                    'checkPoint',
                    123,
                    1.23,
                    function () {
                    },
                    [],
                    new stdClass(),
                ]
            ]
        ];
    }
}
