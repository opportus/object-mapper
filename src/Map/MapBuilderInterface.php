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

use Opportus\ObjectMapper\PathFinding\PathFindingInterface;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;

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
     * Gets the route builder which adds built routes to the map builder.
     *
     * @return RouteBuilderInterface
     */
    public function getRouteBuilder(): RouteBuilderInterface;

    /**
     * Adds the routes.
     *
     * @param RouteCollection $routes
     * @return MapBuilderInterface
     */
    public function addRoutes(RouteCollection $routes): MapBuilderInterface;

    /**
     * Sets the pathfinding.
     *
     * @param null|PathFindingInterface $pathFinding
     * @return MapBuilderInterface
     */
    public function setPathFinding(
        ?PathFindingInterface $pathFinding = null
    ): MapBuilderInterface;

    /**
     * Gets the map.
     *
     * @return Map
     */
    public function getMap(): Map;
}
