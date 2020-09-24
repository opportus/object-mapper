<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinder;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;

/**
 * The path finder interface.
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PathFinderInterface
{
    /**
     * Gets the routes, each connecting a source point to a target point.
     *
     * @param Source $source
     * @param Target $target
     * @return RouteCollection
     * @throws InvalidOperationException
     */
    public function getRoutes(Source $source, Target $target): RouteCollection;
}
