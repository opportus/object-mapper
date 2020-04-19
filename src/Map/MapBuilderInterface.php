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

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\PathFinding\PathFindingInterface;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;

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
     * Prepares the route.
     *
     * @return RouteBuilderInterface
     */
    public function prepareRoute(): RouteBuilderInterface;

    /**
     * Adds the route.
     *
     * @param Route $route
     * @return MapBuilderInterface
     */
    public function addRoute(Route $route): MapBuilderInterface;

    /**
     * Gets the map.
     *
     * @param bool|PathFindingInterface $pathFinding
     * @return Map
     * @throws InvalidArgumentException
     */
    public function getMap($pathFinding = false): Map;
}
