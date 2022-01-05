<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Route;

use Opportus\ObjectMapper\Point\CheckPointCollection;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;

/**
 * The route interface.
 *
 * @package Opportus\ObjectMapper\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface RouteInterface
{
    /**
     * Gets the Fully Qualified Name of the route.
     *
     * @return string The Fully Qualified Name of the route:
     *                SourcePointFqn:TargetPointFqn
     */
    public function getFqn(): string;

    /**
     * Get the source point of the route.
     *
     * @return SourcePointInterface The source point of the route to get value
     *                              from
     */
    public function getSourcePoint(): SourcePointInterface;

    /**
     * Get the target point of the route.
     *
     * @return TargetPointInterface The target point of the route to assign the
     *                              value to
     */
    public function getTargetPoint(): TargetPointInterface;

    /**
     * Gets the check points of the route.
     *
     * @return CheckPointCollection The collection of check points through
     *                              which the value will pass prior to get
     *                              assigned to the target
     */
    public function getCheckPoints(): CheckPointCollection;
}
