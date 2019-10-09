<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Filter;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Opportus\ObjectMapper\AbstractImmutableCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Filter\Filter;
use Opportus\ObjectMapper\Map\Filter\FilterCollection;
use Opportus\ObjectMapper\Map\Filter\FilterInterface;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The filter collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Filter
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class FilterCollectionTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideFilters
     */
    public function testConstruct(array $filters): void
    {
        $filterCollection = new FilterCollection($filters);

        $this->assertInstanceOf(FilterCollection::class, $filterCollection);
        $this->assertInstanceOf(AbstractImmutableCollection::class, $filterCollection);
        $this->assertInstanceOf(ArrayAccess::class, $filterCollection);
        $this->assertInstanceOf(Countable::class, $filterCollection);
        $this->assertInstanceOf(IteratorAggregate::class, $filterCollection);
        $this->assertContainsOnlyInstancesOf(FilterInterface::class, $filterCollection);
        $this->assertSame(\count($filters), \count($filterCollection));

        foreach ($filters as $filterPriority => $filter) {
            $this->assertArrayHasKey($filterPriority, $filterCollection);
            $this->assertSame($filter, $filterCollection[$filterPriority]);
        }
    }

    /**
     * @dataProvider provideInvalidFilters
     */
    public function testConstructException(array $filters): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FilterCollection($filters);
    }

    /**
     * @dataProvider provideFilters
     */
    public function testToArray(array $filters): void
    {
        $filterCollection = new FilterCollection($filters);

        $this->assertSame($filters, $filterCollection->toArray());
    }

    /**
     * @dataProvider provideFilters
     */
    public function testGetIterator(array $filters): void
    {
        $filterCollection = new FilterCollection($filters);
        $iterator = $filterCollection->getIterator();

        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertSame(\count($filters), \count($iterator));

        foreach ($filters as $filterPriority => $filter) {
            $this->assertArrayHasKey($filterPriority, $iterator);
            $this->assertSame($filter, $iterator[$filterPriority]);
        }
    }

    /**
     * @dataProvider provideFilters
     */
    public function testCount(array $filters): void
    {
        $filterCollection = new FilterCollection($filters);

        $this->assertSame(\count($filters), $filterCollection->count());
    }

    /**
     * @dataProvider provideFilters
     */
    public function testOffsetExists(array $filters): void
    {
        $filterCollection = new FilterCollection($filters);

        foreach ($filters as $filterPriority => $filter) {
            $this->assertTrue($filterCollection->offsetExists($filterPriority));
        }

        $this->assertFalse($filterCollection->offsetExists(4));
    }

    /**
     * @dataProvider provideFilters
     */
    public function testOffsetGet(array $filters): void
    {
        $filterCollection = new FilterCollection($filters);

        foreach ($filters as $filterPriority => $filter) {
            $this->assertSame($filter, $filterCollection->offsetGet($filterPriority));
        }
    }

    /**
     * @dataProvider provideFilters
     */
    public function testOffsetSet(array $filters): void
    {
        $filterCollection = new FilterCollection($filters);

        $this->expectException(InvalidOperationException::class);
        $filterCollection->offsetSet(0, null);
    }

    /**
     * @dataProvider provideFilters
     */
    public function testOffsetUnset(array $filters): void
    {
        $filterCollection = new FilterCollection($filters);

        $this->expectException(InvalidOperationException::class);
        $filterCollection->offsetUnset(0);
    }

    public function provideFilters(): array
    {
        $filters = [];
        for ($i = 0; $i < 3; $i++) {
            $filter = $this->getMockBuilder(Filter::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;

            $filters[$i] = $filter;
        }

        return [[$filters]];
    }

    public function provideInvalidFilters(): array
    {
        return [[
            [
                'filter',
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
