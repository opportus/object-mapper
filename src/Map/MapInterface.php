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
     * @param SourceInterface $source
     * @param TargetInterface $target
     * @return RouteCollection
     */
    public function getRoutes(SourceInterface $source, TargetInterface $target): RouteCollection;
}
