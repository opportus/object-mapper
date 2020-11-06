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

use Opportus\ObjectMapper\Map\Map;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\Tests\Test;

/**
 * The map builder test.
 *
 * @package Opportus\ObjectMapper\Tests\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapBuilderTest extends Test
{
    public function testConstruct(): void
    {
        $mapBuilder = $this->createMapBuilder();

        static::assertInstanceOf(MapBuilderInterface::class, $mapBuilder);
    }

    public function testGetRouteBuilder(): void
    {
        $mapBuilder = $this->createMapBuilder();

        $routeBuilder = $mapBuilder->getRouteBuilder();

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder);
        static::assertEquals($mapBuilder, $routeBuilder->getMapBuilder());
    }

    /**
     * @dataProvider provideRoute
     */
    public function testAddRoute(RouteInterface $route): void
    {
        $mapBuilder1 = $this->createMapBuilder();
        $mapBuilder2 = $mapBuilder1->addRoute($route);

        static::assertInstanceOf(MapBuilder::class, $mapBuilder2);
        static::assertNotEquals($mapBuilder1, $mapBuilder2);
    }

    public function testAddRoutes(): void
    {
        $mapBuilder1 = $this->createMapBuilder();

        $routes = [];

        foreach ($this->provideRoute() as $route) {
            $routes[] = $route[0];
        }

        $routes = new RouteCollection($routes);

        $mapBuilder2 = $mapBuilder1->addRoutes($routes);

        static::assertInstanceOf(MapBuilder::class, $mapBuilder2);
        static::assertNotEquals($mapBuilder1, $mapBuilder2);
    }

    public function testAddPathFinder(): void
    {
        $mapBuilder1 = $this->createMapBuilder();
        $mapBuilder2 = $mapBuilder1->addPathFinder(
            $this->createPathFinder()
        );

        static::assertInstanceOf(MapBuilder::class, $mapBuilder2);
        static::assertNotEquals($mapBuilder1, $mapBuilder2);
    }

    public function testAddStaticPathFinder(): void
    {
        $mapBuilder1 = $this->createMapBuilder();
        $mapBuilder2 = $mapBuilder1->addStaticPathFinder();

        static::assertInstanceOf(MapBuilder::class, $mapBuilder2);
        static::assertNotEquals($mapBuilder1, $mapBuilder2);
    }

    public function testAddStaticSourceToDynamicTargetPathFinder(): void
    {
        $mapBuilder1 = $this->createMapBuilder();
        $mapBuilder2 = $mapBuilder1->addStaticSourceToDynamicTargetPathFinder();

        static::assertInstanceOf(MapBuilder::class, $mapBuilder2);
        static::assertNotEquals($mapBuilder1, $mapBuilder2);
    }

    public function testAddDynamicSourceToStaticTargetPathFinder(): void
    {
        $mapBuilder1 = $this->createMapBuilder();
        $mapBuilder2 = $mapBuilder1->addDynamicSourceToStaticTargetPathFinder();

        static::assertInstanceOf(MapBuilder::class, $mapBuilder2);
        static::assertNotEquals($mapBuilder1, $mapBuilder2);
    }

    public function testGetMap(): void
    {
        $mapBuilder = $this->createMapBuilder();

        foreach ($this->provideRoute() as $route) {
            $mapBuilder = $mapBuilder->addRoute($route[0]);
        }

        $map = $mapBuilder
            ->addPathFinder($this->createPathFinder(), 20)
            ->addPathFinder($this->createPathFinder(), 30)
            ->addPathFinder($this->createPathFinder(), 10)
            ->addPathFinder($this->createPathFinder())
            ->addStaticPathFinder(40)
            ->addStaticPathFinder()
            ->addStaticSourceToDynamicTargetPathFinder(10)
            ->addStaticSourceToDynamicTargetPathFinder()
            ->addDynamicSourceToStaticTargetPathFinder(50)
            ->getMap();

        static::assertInstanceOf(Map::class, $map);

        $map = $mapBuilder->getMap();

        static::assertInstanceOf(Map::class, $map);
    }

    private function createPathFinder(): PathFinderInterface
    {
        return new StaticPathFinder($this->createRouteBuilder());
    }
}
