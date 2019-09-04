<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Map\Filter;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Filter\Filter;
use Opportus\ObjectMapper\Map\Filter\FilterCollection;
use Opportus\ObjectMapper\Map\Route\Route;
use PHPUnit\Framework\TestCase;

/**
 * The filter collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Filter
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class FilterCollectionTest extends TestCase
{
    public function testFilterCollectionConstruction(): void
    {
        $filters = [];
        for ($i = 0; $i < 3; $i++) {
            $route = $this->getMockBuilder(Route::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $route->method('getFqn')
                ->willReturn(\sprintf('route_%d', $i))
            ;

            $filters[$route->getFqn()] = new Filter($route, function () {
            });
        }

        $filterCollection = new FilterCollection($filters);

        $this->assertContainsOnlyInstancesOf(Filter::class, $filterCollection);

        foreach ($filters as $filterId => $filter) {
            $this->assertArrayHasKey($filterId, $filterCollection);
            $this->assertSame($filter, $filterCollection[$filterId]);
        }
    }

    /**
     * @dataProvider provideInvalidTypedFilters
     */
    public function testFilterCollectionConstructionException($filters): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FilterCollection($filters);
    }

    public function provideInvalidTypedFilters(): array
    {
        $validTypedFilter = $this->getMockBuilder(Filter::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return [
            [['filter', $validTypedFilter]],
            [[123, $validTypedFilter]],
            [[1.23, $validTypedFilter]],
            [[function () {
            }, $validTypedFilter]],
            [[[], $validTypedFilter]],
            [[new \StdClass(), $validTypedFilter]],

            [[$validTypedFilter, 'filter']],
            [[$validTypedFilter, 123]],
            [[$validTypedFilter, 1.23]],
            [[$validTypedFilter, function () {
            }]],
            [[$validTypedFilter, []]],
            [[$validTypedFilter, new \StdClass()]]
        ];
    }
}
