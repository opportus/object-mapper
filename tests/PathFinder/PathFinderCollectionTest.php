<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
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
use Opportus\ObjectMapper\Tests\Test;
use stdClass;

/**
 * The path finder collectiont test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PathFinderCollectionTest extends Test
{
    /**
     * @dataProvider provideConstructArgument
     */
    public function testConstruct(array $pathFinders): void
    {
        $pathFinderCollection = $this->createPathFinderCollection($pathFinders);

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

        foreach ($pathFinders as $key => $pathFinder) {
            static::assertArrayHasKey(
                $key,
                $pathFinderCollection
            );

            static::assertSame(
                $pathFinder,
                $pathFinderCollection[$key]
            );
        }

        $i = 1;

        foreach ($pathFinderCollection as $key => $pathFinder) {
            static::assertSame(
                $i*10,
                $key
            );

            $i++;
        }
    }

    /**
     * @dataProvider provideInvalidConstructArgument
     */
    public function testConstructException(array $pathFinders): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createPathFinderCollection($pathFinders);
    }

    /**
     * @dataProvider provideConstructArgument
     */
    public function testToArray(array $pathFinders): void
    {
        $pathFinderCollection = $this->createPathFinderCollection($pathFinders);

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

        foreach ($pathFinderCollection->toArray() as $key => $pathFinder) {
            static::assertSame(
                $i*10,
                $key
            );

            $i++;
        }
    }

    /**
     * @dataProvider provideConstructArgument
     */
    public function testGetIterator(array $pathFinders): void
    {
        $pathFinderCollection = $this->createPathFinderCollection($pathFinders);

        $iterator = $pathFinderCollection->getIterator();

        static::assertInstanceOf(ArrayIterator::class, $iterator);
        static::assertSame(\count($pathFinders), \count($iterator));

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            static::assertArrayHasKey($pathFinderPriority, $iterator);
            static::assertSame($pathFinder, $iterator[$pathFinderPriority]);
        }

        $i = 1;

        foreach ($pathFinderCollection->toArray() as $key => $pathFinder) {
            static::assertSame(
                $i*10,
                $key
            );

            $i++;
        }
    }

    /**
     * @dataProvider provideConstructArgument
     */
    public function testCount(array $pathFinders): void
    {
        $pathFinderCollection = $this->createPathFinderCollection($pathFinders);

        static::assertSame(
            \count($pathFinders),
            $pathFinderCollection->count()
        );
    }

    /**
     * @dataProvider provideConstructArgument
     */
    public function testOffsetExists(array $pathFinders): void
    {
        $pathFinderCollection = $this->createPathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            static::assertTrue(
                $pathFinderCollection->offsetExists($pathFinderPriority)
            );
        }

        static::assertFalse($pathFinderCollection->offsetExists(0));
    }

    /**
     * @dataProvider provideConstructArgument
     */
    public function testOffsetGet(array $pathFinders): void
    {
        $pathFinderCollection = $this->createPathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            static::assertSame(
                $pathFinder,
                $pathFinderCollection->offsetGet($pathFinderPriority)
            );
        }
    }

    /**
     * @dataProvider provideConstructArgument
     */
    public function testOffsetSet(array $pathFinders): void
    {
        $pathFinderCollection = $this->createPathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            $this->expectException(InvalidOperationException::class);

            $pathFinderCollection->offsetSet($pathFinderPriority, $pathFinder);
        }
    }

    /**
     * @dataProvider provideConstructArgument
     */
    public function testOffsetUnset(array $pathFinders): void
    {
        $pathFinderCollection = $this->createPathFinderCollection($pathFinders);

        foreach ($pathFinders as $pathFinderPriority => $pathFinder) {
            $this->expectException(InvalidOperationException::class);

            $pathFinderCollection->offsetUnset($pathFinderPriority);
        }
    }

    public function provideConstructArgument(): array
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

    public function provideInvalidConstructArgument(): array
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
                ],
            ],
        ];
    }

    private function createPathFinderCollection(
        array $pathFinders
    ): PathFinderCollection {
        return new PathFinderCollection($pathFinders);
    }
}
