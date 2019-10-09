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

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Filter\Filter;
use Opportus\ObjectMapper\Map\Filter\FilterInterface;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\ObjectMapperInterface;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;
use TypeError;

/**
 * The filter test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Filter
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class FilterTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideRoutes
     */
    public function testConstruct(Route $route): void
    {
        $filter = new Filter($route, function () {
        });

        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertInstanceOf(FilterInterface::class, $filter);
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testConstructException(Route $route): void
    {
        $this->expectException(TypeError::class);
        $filter = new Filter();
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testSupportRoute(Route $route): void
    {
        $filter = new Filter($route, function () {
        });

        $this->assertTrue($filter->supportRoute($route));
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testGetValue(Route $route): void
    {
        $context = $this->buildContext();
        $objectMapper = $this->buildObjectMapper();
        $routeFqn = $route->getFqn();

        $filter = new Filter($route, function ($route, $context, $objectMapper) use ($routeFqn) {
            return
                $route instanceof Route &&
                $context instanceof Context &&
                $objectMapper instanceof ObjectMapperInterface &&
                $route->getFqn() === $routeFqn
            ;
        });

        $this->assertTrue($filter->getValue($route, $context, $objectMapper));
    }

    public function provideRoutes(): array
    {
        $routes = [];
        for ($i = 0; $i < 3; $i++) {
            $route = $this->getMockBuilder(Route::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $route->method('getFqn')
                ->willReturn(\sprintf('route_%d', $i))
            ;

            $routes[] = $route;
        }

        return [$routes];
    }

    private function buildContext(): Context
    {
        return $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    private function buildObjectMapper(): ObjectMapperInterface
    {
        return $this->getMockBuilder(ObjectMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
