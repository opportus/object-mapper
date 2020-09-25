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
     * Sets the static source point of the route.
     *
     * @param string $sourcePointFqn
     * @return RouteBuilderInterface
     */
    public function setStaticSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the static target point of the route.
     *
     * @param string $targetPointFqn
     * @return RouteBuilderInterface
     */
    public function setStaticTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the dynamic source point of the route.
     *
     * @param string $sourcePointFqn
     * @return RouteBuilderInterface
     */
    public function setDynamicSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the dynamic target point of the route.
     *
     * @param string $targetPointFqn
     * @return RouteBuilderInterface
     */
    public function setDynamicTargetPoint(
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
     * @return RouteInterface
     * @throws InvalidOperationException
     */
    public function getRoute(): RouteInterface;

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
