<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route;

/**
 * The route builder interface.
 *
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface RouteBuilderInterface
{
    /**
     * Builds a route.
     *
     * @param string $sourcePointFqn
     * @param string $targetPointFqn
     * @return Route
     */
    public function buildRoute(string $sourcePointFqn, string $targetPointFqn): Route;
}
