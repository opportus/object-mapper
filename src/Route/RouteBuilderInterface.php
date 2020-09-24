<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Route;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Point\CheckPointInterface;

/**
 * The route builder interface.
 *
 * @package Opportus\ObjectMapper\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface RouteBuilderInterface
{
    /**
     * Sets the map builder.
     *
     * @param MapBuilderInterface $mapBuilder
     * @return RouteBuilderInterface
     */
    public function setMapBuilder(
        MapBuilderInterface $mapBuilder
    ): RouteBuilderInterface;

    /**
     * Sets the source point of the route.
     *
     * @param string $sourcePointFqn
     * @return RouteBuilderInterface
     */
    public function setSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the target point of the route.
     *
     * @param string $targetPointFqn
     * @return RouteBuilderInterface
     */
    public function setTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the overloaded source point of the route.
     *
     * @param string $sourcePointFqn
     * @return RouteBuilderInterface
     */
    public function setOverloadedSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the overloaded target point of the route.
     *
     * @param string $targetPointFqn
     * @return RouteBuilderInterface
     */
    public function setOverloadedTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface;

    /**
     * Adds a checkpoint to the route.
     *
     * @param CheckPointInterface $checkPoint
     * @param int $checkPointPosition
     * @return RouteBuilderInterface
     */
    public function addCheckPoint(
        CheckPointInterface $checkPoint,
        int $checkPointPosition = null
    ): RouteBuilderInterface;

    /**
     * Gets the built route.
     *
     * @return Route
     * @throws InvalidOperationException
     */
    public function getRoute(): Route;

    /**
     * Adds the built route to the map builder.
     *
     * @return RouteBuilderInterface
     * @throws InvalidOperationException
     */
    public function addRouteToMapBuilder(): RouteBuilderInterface;

    /**
     * Gets the map builder.
     *
     * @return MapBuilderInterface
     */
    public function getMapBuilder(): MapBuilderInterface;
}
