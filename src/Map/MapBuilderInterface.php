<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\PathFinder\DynamicSourceToStaticTargetPathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
use Opportus\ObjectMapper\PathFinder\StaticSourceToDynamicTargetPathFinder;
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
     * @param  null|int            $priority   The priority of the path finder
     *                                         on the map being built
     * @return MapBuilderInterface             A map builder
     */
    public function addPathFinder(
        PathFinderInterface $pathFinder,
        ?int $priority = null
    ): MapBuilderInterface;

    /**
     * Adds a static path finder to the map being built.
     *
     * @param  null|int            $priority      The priority of the path
     *                                            finder on the map being built
     * @param  bool                $recursiveMode Whether to recurse mapping on
     *                                            aggregate's objects
     * @return MapBuilderInterface                A map builder
     * @see    StaticPathFinder
     */
    public function addStaticPathFinder(
        ?int $priority = null,
        bool $recursiveMode = true
    ): MapBuilderInterface;

    /**
     * Adds a static source to dynamic target path finder to the map being
     * built.
     *
     * @param  null|int            $priority      The priority of the path
     *                                            finder on the map being built
     * @param  bool                $recursiveMode Whether to recurse mapping on
     *                                            aggregate's objects
     * @return MapBuilderInterface                A map builder
     * @see    StaticSourceToDynamicTargetPathFinder
     */
    public function addStaticSourceToDynamicTargetPathFinder(
        ?int $priority = null,
        bool $recursiveMode = true
    ): MapBuilderInterface;

    /**
     * Adds a dynamic source to static target path finder to the map being
     * built.
     *
     * @param  null|int            $priority      The priority of the path
     *                                            finder on the map being built
     * @param  bool                $recursiveMode Whether to recurse mapping on
     *                                            aggregate's objects
     * @return MapBuilderInterface                A map builder
     * @see    DynamicSourceToStaticTargetPathFinder
     */
    public function addDynamicSourceToStaticTargetPathFinder(
        ?int $priority = null,
        bool $recursiveMode = true
    ): MapBuilderInterface;

    /**
     * Gets the built map.
     *
     * @return MapInterface The map built
     */
    public function getMap(): MapInterface;
}
