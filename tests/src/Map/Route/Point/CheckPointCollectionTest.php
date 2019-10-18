<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Route\Point;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Opportus\ObjectMapper\AbstractImmutableCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointInterface;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The check point collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class CheckPointCollectionTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideCheckPoints
     */
    public function testConstruct(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        $this->assertInstanceOf(CheckPointCollection::class, $checkPointCollection);
        $this->assertInstanceOf(AbstractImmutableCollection::class, $checkPointCollection);
        $this->assertInstanceOf(ArrayAccess::class, $checkPointCollection);
        $this->assertInstanceOf(Countable::class, $checkPointCollection);
        $this->assertInstanceOf(IteratorAggregate::class, $checkPointCollection);
        $this->assertContainsOnlyInstancesOf(CheckPointInterface::class, $checkPointCollection);
        $this->assertSame(\count($checkPoints), \count($checkPointCollection));

        foreach ($checkPoints as $checkPointPosition=> $checkPoint) {
            $this->assertArrayHasKey($checkPointPosition, $checkPointCollection);
            $this->assertSame($checkPoint, $checkPointCollection[$checkPointPosition]);
        }
    }

    /**
     * @dataProvider provideInvalidCheckPoints
     */
    public function testConstructException(array $checkPoints): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CheckPointCollection($checkPoints);
    }

    /**
     * @dataProvider provideCheckPoints
     */
    public function testToArray(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        $this->assertSame($checkPoints, $checkPointCollection->toArray());
    }

    /**
     * @dataProvider provideCheckPoints
     */
    public function testGetIterator(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);
        $iterator = $checkPointCollection->getIterator();

        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertSame(\count($checkPoints), \count($iterator));

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            $this->assertArrayHasKey($checkPointPosition, $iterator);
            $this->assertSame($checkPoint, $iterator[$checkPointPosition]);
        }
    }

    /**
     * @dataProvider provideCheckPoints
     */
    public function testCount(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        $this->assertSame(\count($checkPoints), $checkPointCollection->count());
    }

    /**
     * @dataProvider provideCheckPoints
     */
    public function testOffsetExists(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            $this->assertTrue($checkPointCollection->offsetExists($checkPointPosition));
        }

        $this->assertFalse($checkPointCollection->offsetExists(4));
    }

    /**
     * @dataProvider provideCheckPoints
     */
    public function testOffsetGet(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        foreach ($checkPoints as $checkPointPosition => $checkPoint) {
            $this->assertSame($checkPoint, $checkPointCollection->offsetGet($checkPointPosition));
        }
    }

    /**
     * @dataProvider provideCheckPoints
     */
    public function testOffsetSet(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        $this->expectException(InvalidOperationException::class);
        $checkPointCollection->offsetSet(0, null);
    }

    /**
     * @dataProvider provideCheckPoints
     */
    public function testOffsetUnset(array $checkPoints): void
    {
        $checkPointCollection = new CheckPointCollection($checkPoints);

        $this->expectException(InvalidOperationException::class);
        $checkPointCollection->offsetUnset(0);
    }

    public function provideCheckPoints(): array
    {
        $checkPoints = [];
        for ($i = 0; $i < 3; $i++) {
            $checkPoint = $this->getMockBuilder(CheckPointInterface::class)
                ->getMock()
            ;

            $checkPoints[$i] = $checkPoint;
        }

        return [[$checkPoints]];
    }

    public function provideInvalidCheckPoints(): array
    {
        return [[
            [
                'checkPoint',
                123,
                1.23,
                function () {
                },
                [],
                new \StdClass(),
            ]
        ]];
    }
}
