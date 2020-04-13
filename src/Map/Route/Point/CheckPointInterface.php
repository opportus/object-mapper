<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;

/**
 * The check point interface.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface CheckPointInterface
{
    /**
     * Controls the value.
     *
     * @param mixed $value The value going to be assigned to the target point
     * @param Route $route The route which the mapper is currently on
     * @param Source $source The source which the point value comes from
     * @param Target $target The target which the point value goes to
     * @return mixed The value going to be assigned to the target point
     */
    public function control(
        $value,
        Route $route,
        Source $source,
        Target $target
    );
}
