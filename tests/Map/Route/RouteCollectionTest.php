<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Map\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use PHPUnit\Framework\TestCase;

/**
 * The route collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteCollectionTest extends TestCase
{
    public function testRouteCollectionConstruction(): void
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

            $routes[$route->getFqn()] = $route;
        }

        $routeCollection = new RouteCollection($routes);

        $this->assertContainsOnlyInstancesOf(Route::class, $routeCollection);

        foreach ($routes as $routeId => $route) {
            $this->assertArrayHasKey($routeId, $routeCollection);
            $this->assertSame($route, $routeCollection[$routeId]);
        }
    }

    /**
     * @dataProvider provideInvalidTypedRoutes
     */
    public function testRouteCollectionConstructionException($route): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RouteCollection([$route]);
    }

    public function provideInvalidTypedRoutes(): array
    {
        return [
            ['route'],
            [123],
            [1.23],
            [function () {}],
            [[]],
            [new \StdClass()]
        ];
    }
}

