<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Route;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\ImmutableCollection;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Route\RouteInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * The route collection test.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteCollectionTest extends TestCase
{
    /**
     * @dataProvider provideConstructArguments
     */
    public function testConstruct(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        static::assertInstanceOf(
            RouteCollection::class,
            $routeCollection
        );

        static::assertInstanceOf(
            ImmutableCollection::class,
            $routeCollection
        );

        static::assertInstanceOf(
            ArrayAccess::class,
            $routeCollection
        );

        static::assertInstanceOf(
            Countable::class,
            $routeCollection
        );

        static::assertInstanceOf(
            IteratorAggregate::class,
            $routeCollection
        );

        static::assertContainsOnlyInstancesOf(
            RouteInterface::class,
            $routeCollection
        );

        static::assertSame(
            \count($routes),
            \count($routeCollection)
        );

        foreach ($routes as $routeFqn => $route) {
            static::assertArrayHasKey($routeFqn, $routeCollection);
            static::assertSame($route, $routeCollection[$routeFqn]);
        }
    }

    /**
     * @dataProvider provideConstructInvalidArguments
     */
    public function testConstructException(array $routes): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RouteCollection($routes);
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testToArray(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        foreach ($routes as $routeFqn => $route) {
            static::assertArrayHasKey(
                $routeFqn,
                $routeCollection->toArray()
            );

            static::assertSame(
                $route,
                $routeCollection->toArray()[$routeFqn]
            );
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testGetIterator(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);
        $iterator = $routeCollection->getIterator();

        static::assertInstanceOf(ArrayIterator::class, $iterator);
        static::assertSame(\count($routes), \count($iterator));

        foreach ($routes as $routeFqn => $route) {
            static::assertArrayHasKey($routeFqn, $iterator);
            static::assertSame($route, $iterator[$routeFqn]);
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testCount(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        static::assertSame(\count($routes), $routeCollection->count());
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetExists(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        foreach ($routes as $routeFqn => $route) {
            static::assertTrue($routeCollection->offsetExists($routeFqn));
        }

        static::assertFalse($routeCollection->offsetExists('route_4'));
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetGet(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        foreach ($routes as $routeFqn => $route) {
            static::assertSame($route, $routeCollection->offsetGet($routeFqn));
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetSet(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        foreach ($routes as $routeFqn => $route) {
            $this->expectException(InvalidOperationException::class);

            $routeCollection->offsetSet($routeFqn, $route);
        }
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testOffsetUnset(array $routes): void
    {
        $routeCollection = new RouteCollection($routes);

        foreach ($routes as $routeFqn => $route) {
            $this->expectException(InvalidOperationException::class);

            $routeCollection->offsetUnset($routeFqn);
        }
    }

    public function provideConstructArguments(): array
    {
        $routes = [];
        for ($i = 0; $i < 3; $i++) {
            $route = $this->getMockBuilder(RouteInterface::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $route->method('getFqn')
                ->willReturn(\sprintf('route_%d', $i))
            ;

            $routes[$route->getFqn()] = $route;
        }

        return [
            [
                $routes
            ]
        ];
    }

    public function provideConstructInvalidArguments(): array
    {
        return [
            [
                [
                    'route',
                    123,
                    1.23,
                    function () {
                    },
                    [],
                    new stdClass(),
                ]
            ]
        ];
    }
}
