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

use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Route\RouteInterface;

/**
 * The map builder interface.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapBuilderInterface
{
    /**
     * Gets the route builder.
     *
     * @return RouteBuilderInterface A `RouteBuilderInterface` instance
     */
    public function getRouteBuilder(): RouteBuilderInterface;

    /**
     * Adds a route to the map being built.
     *
     * @param  RouteInterface      $route A route to add to the map being built
     * @return MapBuilderInterface        A map builder
     */
    public function addRoute(RouteInterface $route): MapBuilderInterface;

    /**
     * Adds the routes to the map being built.
     *
     * @param  RouteCollection     $routes A collection of routes to add to the
     *                                     map being built
     * @return MapBuilderInterface         A map builder
     */
    public function addRoutes(RouteCollection $routes): MapBuilderInterface;

    /**
     * Adds a path finder to the map being built.
     *
     * @param  PathFinderInterface $pathFinder A path finder to add to the map
     *                                         being built
     * @return MapBuilderInterface             A map builder
     */
    public function addPathFinder(
        PathFinderInterface $pathFinder
    ): MapBuilderInterface;

    /**
     * Adds a static path finder to the map being built.
     *
     * @return MapBuilderInterface A map builder
     */
    public function addStaticPathFinder(): MapBuilderInterface;

    /**
     * Adds a dynamic path finder to the map being built.
     *
     * @return MapBuilderInterface A map builder
     */
    public function addDynamicPathFinder(): MapBuilderInterface;

    /**
     * Gets the built map.
     *
     * @return MapInterface The map built
     */
    public function getMap(): MapInterface;
}
