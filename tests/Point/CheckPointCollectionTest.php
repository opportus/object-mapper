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
use Opportus\ObjectMapper\Tests\Test;
use stdClass;

/**
 * The check point collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class CheckPointCollectionTest extends Test
{
    /**
     * @dataProvider provideConstructArguments
     */
    public function testConstruct(array $checkPoints): void
    {
        $checkPointCollection = $this->createCheckPointCollection($checkPoints);

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

        foreach ($checkPoints as $index => $checkPoint) {
            static::assertArrayHasKey(
                $index,
                $checkPointCollection
            );

            static::assertSame(
                $checkPoint,
                $checkPointCollection[$index]
            );
        }

        $i = 1;

        foreach ($checkPointCollection as $index => $checkPoint) {
            static::assertSame(
                $i*10,
                $index
            );

            $i++;
        }
    }

    /**
     * @dataProvider provideConstructInvalidArguments
     */
    public function testConstructException(array $checkPoints): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createCheckPointCollection($checkPoints);
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testToArray(array $checkPoints): void
    {
        $checkPointCollection = $this->createCheckPointCollection($checkPoints);

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

        $i = 1;

        foreach ($checkPointCollection->toArray() as $index => $checkPoint) {
            static::assertSame(
                $i*10,
                $index
            );

            $i++;
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testGetIterator(array $checkPoints): void
    {
        $checkPointCollection = $this->createCheckPointCollection($checkPoints);

        $iterator = $checkPointCollection->getIterator();

        static::assertInstanceOf(ArrayIterator::class, $iterator);
        static::assertSame(\count($checkPoints), \count($iterator));

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            static::assertArrayHasKey($checkPointPosition, $iterator);
            static::assertSame($checkPoint, $iterator[$checkPointPosition]);
        }

        $i = 1;

        foreach ($checkPointCollection->toArray() as $index => $checkPoint) {
            static::assertSame(
                $i*10,
                $index
            );

            $i++;
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testCount(array $checkPoints): void
    {
        $checkPointCollection = $this->createCheckPointCollection($checkPoints);

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
        $checkPointCollection = $this->createCheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            static::assertTrue(
                $checkPointCollection->offsetExists($checkPointPosition)
            );
        }

        static::assertFalse($checkPointCollection->offsetExists(0));
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetGet(array $checkPoints): void
    {
        $checkPointCollection = $this->createCheckPointCollection($checkPoints);

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
        $checkPointCollection = $this->createCheckPointCollection($checkPoints);

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
        $checkPointCollection = $this->createCheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            $this->expectException(InvalidOperationException::class);

            $checkPointCollection->offsetUnset($checkPointPosition);
        }
    }

    public function provideConstructArguments(): array
    {
        return [
            [
                [
                    40 => $this->getMockBuilder(CheckPointInterface::class)
                        ->getMock(),
                    20=> $this->getMockBuilder(CheckPointInterface::class)
                        ->getMock(),
                    30 => $this->getMockBuilder(CheckPointInterface::class)
                        ->getMock(),
                    50 => $this->getMockBuilder(CheckPointInterface::class)
                        ->getMock(),
                    10 => $this->getMockBuilder(CheckPointInterface::class)
                        ->getMock(),
                ],
            ],
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
                ],
            ],
            [
                [
                    'a' => $this->getMockBuilder(CheckPointInterface::class)
                        ->getMock(),
                    1 => $this->getMockBuilder(CheckPointInterface::class)
                        ->getMock(),
                ]
            ]
        ];
    }

    private function createCheckPointCollection(
        array $checkPoints
    ): CheckPointCollection {
        return new CheckPointCollection($checkPoints);
    }
}
