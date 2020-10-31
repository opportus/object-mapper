<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\PathFinder;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\ImmutableCollection;
use Opportus\ObjectMapper\PathFinder\PathFinderCollection;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * The path finder collectiont test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PathFinderCollectionTest extends TestCase
{
    /**
     * @dataProvider provideConstructArguments
     */
    public function testConstruct(array $pathFinders): void
    {
        $pathFinderCollection = new PathFinderCollection($pathFinders);

        static::assertInstanceOf(
            PathFinderCollection::class,
            $pathFinderCollection
        );

        static::assertInstanceOf(
            ImmutableCollection::class,
            $pathFinderCollection
        );

        static::assertInstanceOf(
            ArrayAccess::class,
            $pathFinderCollection
        );

        static::assertInstanceOf(
            Countable::class,
            $pathFinderCollection
        );

        static::assertInstanceOf(
            IteratorAggregate::class,
            $pathFinderCollection
        );

        static::assertContainsOnlyInstancesOf(
            PathFinderInterface::class,
            $pathFinderCollection
        );

        static::assertEquals(
            \count($pathFinders),
            \count($pathFinderCollection)
        );

        foreach ($pathFinders as $index => $pathFinder) {
            static::assertArrayHasKey(
                $index,
                $pathFinderCollection
            );

            static::assertSame(
                $pathFinder,
                $pathFinderCollection[$index]
            );
        }

        $i = 1;

        foreach ($pathFinderCollection as $index => $pathFinder) {
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
    public function testConstructException(array $pathFinders): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PathFinderCollection($pathFinders);
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testToArray(array $pathFinders): void
    {
        $pathFinderCollection = new PathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            static::assertArrayHasKey(
                $pathFinderPriority,
                $pathFinderCollection->toArray()
            );

            static::assertSame(
                $pathFinder,
                $pathFinderCollection->toArray()[$pathFinderPriority]
            );
        }

        $i = 1;

        foreach ($pathFinderCollection->toArray() as $index => $pathFinder) {
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
    public function testGetIterator(array $pathFinders): void
    {
        $pathFinderCollection = new PathFinderCollection($pathFinders);

        $iterator = $pathFinderCollection->getIterator();

        static::assertInstanceOf(ArrayIterator::class, $iterator);
        static::assertSame(\count($pathFinders), \count($iterator));

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            static::assertArrayHasKey($pathFinderPriority, $iterator);
            static::assertSame($pathFinder, $iterator[$pathFinderPriority]);
        }

        $i = 1;

        foreach ($pathFinderCollection->toArray() as $index => $pathFinder) {
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
    public function testCount(array $pathFinders): void
    {
        $pathFinderCollection = new PathFinderCollection($pathFinders);

        static::assertSame(
            \count($pathFinders),
            $pathFinderCollection->count()
        );
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetExists(array $pathFinders): void
    {
        $pathFinderCollection = new PathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            static::assertTrue(
                $pathFinderCollection->offsetExists($pathFinderPriority)
            );
        }

        static::assertFalse($pathFinderCollection->offsetExists(0));
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetGet(array $pathFinders): void
    {
        $pathFinderCollection = new PathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            static::assertSame(
                $pathFinder,
                $pathFinderCollection->offsetGet($pathFinderPriority)
            );
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetSet(array $pathFinders): void
    {
        $pathFinderCollection = new PathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            $this->expectException(InvalidOperationException::class);

            $pathFinderCollection->offsetSet($pathFinderPriority, $pathFinder);
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetUnset(array $pathFinders): void
    {
        $pathFinderCollection = new PathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            $this->expectException(InvalidOperationException::class);

            $pathFinderCollection->offsetUnset($pathFinderPriority);
        }
    }

    public function provideConstructArguments(): array
    {
        return [
            [
                [
                    40 => $this->getMockBuilder(PathFinderInterface::class)
                        ->getMock(),
                    20=> $this->getMockBuilder(PathFinderInterface::class)
                        ->getMock(),
                    30 => $this->getMockBuilder(PathFinderInterface::class)
                        ->getMock(),
                    50 => $this->getMockBuilder(PathFinderInterface::class)
                        ->getMock(),
                    10 => $this->getMockBuilder(PathFinderInterface::class)
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
                    'pathFinder',
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
                    'a' => $this->getMockBuilder(PathFinderInterface::class)
                        ->getMock(),
                    1 => $this->getMockBuilder(PathFinderInterface::class)
                        ->getMock(),
                ]
            ]
        ];
    }
}
