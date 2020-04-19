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
     * Builds a route.
     *
     * @param string $sourcePointFqn
     * @param string $targetPointFqn
     * @param CheckPointCollection $checkPoints
     * @return Route
     */
    public function buildRoute(
        string $sourcePointFqn,
        string $targetPointFqn,
        CheckPointCollection $checkPoints
    ): Route;
}
