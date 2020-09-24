<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\PathFinder\NoPathFinder;
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;

/**
 * The map builder.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class MapBuilder implements MapBuilderInterface
{
    /**
     * @var RouteBuilderInterface $routeBuilder
     */
    private $routeBuilder;

    /**
     * @var RouteCollection $routes
     */
    private $routes;

    /**
     * @var null|PathFinder
     */
    private $pathFinder;

    /**
     * Constructs the map builder.
     *
     * @param RouteBuilderInterface $routeBuilder
     * @param null|RouteCollection $routes
     * @param null|PathFinderInterface $pathFinder
     */
    public function __construct(
        RouteBuilderInterface $routeBuilder,
        ?RouteCollection $routes = null,
        ?PathFinderInterface $pathFinder = null
    ) {
        $this->routeBuilder = $routeBuilder;
        $this->routes = $routes ?? new RouteCollection();
        $this->pathFinder = $pathFinder;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteBuilder(): RouteBuilderInterface
    {
        return $this->routeBuilder->setMapBuilder($this);
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(Route $route): MapBuilderInterface
    {
        $routes = $this->routes->toArray();

        $routes[] = $route;

        return new self(
            $this->routeBuilder,
            new RouteCollection($routes),
            $this->pathFinder
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addRoutes(RouteCollection $routes): MapBuilderInterface
    {
        $routes = $routes->toArray() + $this->routes->toArray();

        return new self(
            $this->routeBuilder,
            new RouteCollection($routes),
            $this->pathFinder
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setPathFinder(
        ?PathFinderInterface $pathFinder = null
    ): MapBuilderInterface {
        $pathFinder = $pathFinder ?? new StaticPathFinder($this->routeBuilder);

        return new self(
            $this->routeBuilder,
            $this->routes,
            $pathFinder
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMap(): Map
    {
        $pathFinder = $this->pathFinder ?? new NoPathFinder();

        return new Map($pathFinder, $this->routes);
    }
}
