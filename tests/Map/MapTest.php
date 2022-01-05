<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
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
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\Test;
use stdClass;

/**
 * The map test.
 *
 * @package Opportus\ObjectMapper\Tests\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapTest extends Test
{
    public function testConstruct(): void
    {
        $map = $this->createMap();

        static::assertInstanceOf(MapInterface::class, $map);
    }

    /**
     * @dataProvider provideObjects
     */
    public function testGetRoutes(object $providedSource, $providedTarget): void
    {
        $source = new Source($providedSource);
        $target = new Target($providedTarget);

        $pathFinders = $this->createPathFinders();

        $mapRoutes = [];

        foreach ($this->provideRoute() as $route) {
            $mapRoutes[] = $route[0];
        }

        $mapRoutes = new RouteCollection($mapRoutes);

        $map = $this->createMap($pathFinders, $mapRoutes);

        $expectedRoutes = [];

        foreach ($pathFinders as $pathFinder) {
            foreach ($pathFinder->getRoutes($source, $target) as $pathFinderRoute) {
                $expectedRoutes[] = $pathFinderRoute;
            }
        }

        foreach ($mapRoutes as $mapRoute) {
            $sourceFqn = $mapRoute->getSourcePoint()->getSourceFqn();
            $targetFqn = $mapRoute->getTargetPoint()->getTargetFqn();

            if ($sourceFqn !== $source->getFqn() || $targetFqn !== $target->getFqn()) {
                continue;
            }

            $expectedRoutes[] = $mapRoute;
        }

        $expectedRoutesToKeep = [];

        foreach ($expectedRoutes as $key => $route) {
            $expectedRoutesToKeep[$route->getTargetPoint()->getFqn()] = $key;
        }

        foreach ($expectedRoutes as $key => $route) {
            if (false === \in_array($key, $expectedRoutesToKeep)) {
                unset($expectedRoutes[$key]);
            }
        }

        $expectedRoutes = new RouteCollection($expectedRoutes);

        $returnedRoutes = $map->getRoutes($source, $target);

        static::assertEquals($expectedRoutes, $returnedRoutes);

        $returnedRoutes = $map->getRoutes(
            new Source(new stdClass()),
            new Target(stdClass::class)
        );

        static::assertEmpty($returnedRoutes);
    }

    /**
     * @dataProvider provideObjects
     */
    public function testGetRoutesException(
        object $providedSource,
        $providedTarget
    ): void {
        $pathFinder = $this->getMockBuilder(PathFinderInterface::class)
            ->getMock();

        $pathFinder->method('getRoutes')
            ->willThrowException(new Exception());

        $pathFinders = new PathFinderCollection([$pathFinder]);

        $map = $this->createMap($pathFinders);

        $source = new Source($providedSource);
        $target = new Target($providedTarget);

        $this->expectException(InvalidOperationException::class);

        $map->getRoutes($source, $target);
    }

    private function createPathFinders(): PathFinderCollection
    {
        return new PathFinderCollection([
            new StaticPathFinder(
                $this->createRouteBuilder()
            ),
            new StaticSourceToDynamicTargetPathFinder(
                $this->createRouteBuilder()
            ),
            new DynamicSourceToStaticTargetPathFinder(
                $this->createRouteBuilder()
            ),
        ]);
    }

    private function createMap(
        ?PathFinderCollection $pathFinders = null,
        ?RouteCollection $routes = null
    ): Map {
        return new Map($pathFinders, $routes);
    }
}
