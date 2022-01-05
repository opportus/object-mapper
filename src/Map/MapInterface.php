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

use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;

/**
 * The map interface.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapInterface
{
    /**
     * Gets the routes connecting the source points with the target points.
     *
     * @param  SourceInterface           $source A source to map data from
     * @param  TargetInterface           $target A target to map data to
     * @return RouteCollection           A route collection connecting the
     *                                   source points with the target points
     * @throws InvalidOperationException If the operation fails for any reason
     */
    public function getRoutes(SourceInterface $source, TargetInterface $target): RouteCollection;
}
