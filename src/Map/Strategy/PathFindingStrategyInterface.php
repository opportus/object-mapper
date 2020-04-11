<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\RouteCollection;

/**
 * The path finding strategy interface.
 *
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PathFindingStrategyInterface
{
    /**
     * Gets the routes connecting the points of the source with the points of the target.
     *
     * @param Context $context
     * @return RouteCollection
     */
    public function getRoutes(Context $context): RouteCollection;
}
