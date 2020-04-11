<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Route;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Opportus\ObjectMapper\AbstractImmutableCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The route collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteCollectionTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideRoutes
     */
    public function testConstruct(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        $this->assertInstanceOf(RouteCollection::class, $routeCollection);
        $this->assertInstanceOf(AbstractImmutableCollection::class, $routeCollection);
        $this->assertInstanceOf(ArrayAccess::class, $routeCollection);
        $this->assertInstanceOf(Countable::class, $routeCollection);
        $this->assertInstanceOf(IteratorAggregate::class, $routeCollection);
        $this->assertContainsOnlyInstancesOf(Route::class, $routeCollection);
        $this->assertSame(\count($routes), \count($routeCollection));

        foreach ($routes as $routeFqn => $route) {
            $this->assertArrayHasKey($routeFqn, $routeCollection);
            $this->assertSame($route, $routeCollection[$routeFqn]);
        }
    }

    /**
     * @dataProvider provideInvalidRoutes
     */
    public function testConstructException(array $routes): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RouteCollection($routes);
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testToArray(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        $this->assertSame($routes, $routeCollection->toArray());
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testGetIterator(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);
        $iterator = $routeCollection->getIterator();

        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertSame(\count($routes), \count($iterator));

        foreach ($routes as $routeFqn => $route) {
            $this->assertArrayHasKey($routeFqn, $iterator);
            $this->assertSame($route, $iterator[$routeFqn]);
        }
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testCount(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        $this->assertSame(\count($routes), $routeCollection->count());
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testOffsetExists(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        foreach ($routes as $routeFqn => $route) {
            $this->assertTrue($routeCollection->offsetExists($routeFqn));
        }

        $this->assertFalse($routeCollection->offsetExists('route_4'));
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testOffsetGet(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        foreach ($routes as $routeFqn => $route) {
            $this->assertSame($route, $routeCollection->offsetGet($routeFqn));
        }
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testOffsetSet(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        $this->expectException(InvalidOperationException::class);
        $routeCollection->offsetSet('route_0', null);
    }

    /**
     * @dataProvider provideRoutes
     */
    public function testOffsetUnset(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        $this->expectException(InvalidOperationException::class);
        $routeCollection->offsetUnset('route_0');
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

            $routes[$route->getFqn()] = $route;
        }

        return [[$routes]];
    }

    public function provideInvalidRoutes(): array
    {
        return [[
            [
                'route',
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
