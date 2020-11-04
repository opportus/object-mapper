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

use Exception;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Map;
use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\PathFinder\DynamicSourceToStaticTargetPathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinderCollection;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
use Opportus\ObjectMapper\PathFinder\StaticSourceToDynamicTargetPathFinder;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\TestObjectA;
use Opportus\ObjectMapper\Tests\TestObjectB;
use Opportus\ObjectMapper\Tests\TestDataProviderTrait;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * The map test.
 *
 * @package Opportus\ObjectMapper\Tests\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapTest extends TestCase
{
    use TestDataProviderTrait;

    public function testConstruct(): void
    {
        $map = new Map();

        static::assertInstanceOf(MapInterface::class, $map);
    }

    public function testGetRoutes(): void
    {
        $pathFinders = new PathFinderCollection([
            new StaticPathFinder(
                new RouteBuilder(new PointFactory)
            ),
            new StaticSourceToDynamicTargetPathFinder(
                new RouteBuilder(new PointFactory)
            ),
            new DynamicSourceToStaticTargetPathFinder(
                new RouteBuilder(new PointFactory)
            ),
        ]);

        $routes = [];

        foreach ($this->provideRoutePoints() as $routePoints) {
            $routes[] = new Route(
                $routePoints[0],
                $routePoints[1],
                $routePoints[2]
            );
        }

        $routes = new RouteCollection($routes);

        $map = new Map($pathFinders, $routes);

        $source = new Source(new TestObjectA());
        $target = new Target(new TestObjectB());

        $expectedRoutes = [];

        foreach ($pathFinders as $pathFinder) {
            foreach ($pathFinder->getRoutes($source, $target) as $pathFinderRoute) {
                $expectedRoutes[] = $pathFinderRoute;
            }
        }

        foreach ($routes as $route) {
            if (
                $route->getSourcePoint()->getSourceFqn() !== $source->getFqn() ||
                $route->getTargetPoint()->getTargetFqn() !== $target->getFqn()
            ) {
                continue;
            }

            $expectedRoutes[] = $route;
        }

        $expectedRoutes = new RouteCollection($expectedRoutes);

        $returnedRoutes = $map->getRoutes($source, $target);

        static::assertEquals($expectedRoutes, $returnedRoutes);
    }

    public function testGetRouteException(): void
    {
        $pathFinder = $this->getMockBuilder(PathFinderInterface::class)
            ->getMock();

        $pathFinder->method('getRoutes')
            ->willThrowException(new Exception());

        $pathFinderCollection = new PathFinderCollection([$pathFinder]);

        $map = new Map($pathFinderCollection);

        $source = new Source(new TestObjectA());
        $target = new Target(new TestObjectB());

        $this->expectException(InvalidOperationException::class);

        $map->getRoutes($source, $target);
    }
}
