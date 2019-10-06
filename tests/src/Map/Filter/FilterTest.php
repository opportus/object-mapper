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

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Filter\Filter;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\ObjectMapper;
use PHPUnit\Framework\TestCase;

/**
 * The filter test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Filter
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class FilterTest extends TestCase
{
    public function testSupportRouteIsTrue(): void
    {
        $filter = new Filter($this->buildRoute(0), function () {
        });

        $this->assertTrue($filter->supportRoute($this->buildRoute(0)));

        $filter = new Filter($this->buildRoute(0), function () {
        });

        $this->assertFalse($filter->supportRoute($this->buildRoute(1)));
    }

    public function testGetValue(): void
    {
        $callable = function ($route, $context, $objectMapper) {
            return
                $route instanceof Route &&
                $context instanceof Context &&
                $objectMapper instanceof ObjectMapper
            ;
        };

        $route = $this->buildRoute(0);

        $filter = new Filter($route, $callable);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $objectMapper = $this->getMockBuilder(ObjectMapper::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->assertTrue($filter->getValue($route, $context, $objectMapper));
    }

    private function buildRoute(int $id): Route
    {
        $route = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $route
            ->method('getFqn')
            ->willReturn(\sprintf('%d', $id))
        ;

        return $route;
    }
}
