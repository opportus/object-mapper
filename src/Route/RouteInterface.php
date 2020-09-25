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
     * @return string
     */
    public function getFqn(): string;

    /**
     * Get the source point of the route.
     *
     * @return SourcePointInterface
     */
    public function getSourcePoint(): SourcePointInterface;

    /**
     * Get the target point of the route.
     *
     * @return TargetPointInterface
     */
    public function getTargetPoint(): TargetPointInterface;

    /**
     * Gets the checkpoints of the route.
     *
     * @return CheckPointCollection
     */
    public function getCheckPoints(): CheckPointCollection;
}
