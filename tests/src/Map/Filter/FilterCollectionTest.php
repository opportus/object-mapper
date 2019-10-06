<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Filter;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Filter\Filter;
use Opportus\ObjectMapper\Map\Filter\FilterCollection;
use Opportus\ObjectMapper\Map\Route\Route;
use PHPUnit\Framework\TestCase;

/**
 * The filter collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Filter
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

            $filters[$i] = new Filter($route, function () {
            });
        }

        $filterCollection = new FilterCollection($filters);

        $this->assertContainsOnlyInstancesOf(Filter::class, $filterCollection);

        foreach ($filters as $filterIndex => $filter) {
            $this->assertArrayHasKey($filterIndex, $filterCollection);
            $this->assertSame($filter, $filterCollection[$filterIndex]);
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
