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
     * Sets the map builder to which the route builder passes built routes.
     *
     * @param  MapBuilderInterface   $mapBuilder A map builder to add built
     *                                           routes to
     * @return RouteBuilderInterface             A route builder
     */
    public function setMapBuilder(
        MapBuilderInterface $mapBuilder
    ): RouteBuilderInterface;

    /**
     * Sets the static source point of the route.
     *
     * @param  string                $sourcePointFqn The Fully Qualified Name
     *                                               of a static source point
     *                                               to compose the route being
     *                                               build of
     * @return RouteBuilderInterface                 A route builder
     */
    public function setStaticSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the static target point of the route.
     *
     * @param  string                $targetPointFqn The Fully Qualified Name
     *                                               of a static target point
     *                                               to compose the route being
     *                                               built of
     * @return RouteBuilderInterface                 A route builder
     */
    public function setStaticTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the dynamic source point of the route.
     *
     * @param  string                $sourcePointFqn The Fully Qualified Name
     *                                               of a dynamic source point
     *                                               to compose the route being
     *                                               build of
     * @return RouteBuilderInterface                 A route builder
     */
    public function setDynamicSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the dynamic target point of the route.
     *
     * @param  string                $targetPointFqn The Fully Qualified Name
     *                                               of a dynamic target point
     *                                               to compose the route being
     *                                               built of
     * @return RouteBuilderInterface                 A route builder
     */
    public function setDynamicTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the source point of the route.
     *
     * @param  string                $sourcePointFqn The Fully Qualified Name
     *                                               of a static or dynamic
     *                                               source point
     * @return RouteBuilderInterface                 A route builder
     */
    public function setSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface;

    /**
     * Sets the target point of the route.
     *
     * @param  string                $targetPointFqn The Fully Qualified Name
     *                                               of a static or dynamic
     *                                               target point
     * @return RouteBuilderInterface                 A route builder
     */
    public function setTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface;

    /**
     * Adds a checkpoint to the route.
     *
     * @param  CheckPointInterface   $checkPoint         A check point to add to
     *                                                   route being built
     * @param  int                   $checkPointPosition The position of the
     *                                                   check point on the
     *                                                   route being built
     * @return RouteBuilderInterface                     A route builder
     */
    public function addCheckPoint(
        CheckPointInterface $checkPoint,
        int $checkPointPosition = null
    ): RouteBuilderInterface;

    /**
     * Gets the built route.
     *
     * @return RouteInterface            The route built
     * @throws InvalidOperationException If either or both of the source point
     *                                   or the target point have not been set
     *                                   or if the operation fails for any
     *                                   reason
     */
    public function getRoute(): RouteInterface;

    /**
     * Adds the built route to the map builder.
     *
     * @return RouteBuilderInterface     A route builder to which the built
     *                                   route has been added
     * @throws InvalidOperationException If either or both of the source point
     *                                   or the target point have not been set
     *                                   or if the operation fails for any
     *                                   reason
     */
    public function addRouteToMapBuilder(): RouteBuilderInterface;

    /**
     * Gets the map builder.
     *
     * @return null|MapBuilderInterface The map builder to add built routes to
     *                                  if it has been set or NULL otherwise
     */
    public function getMapBuilder(): ?MapBuilderInterface;
}
