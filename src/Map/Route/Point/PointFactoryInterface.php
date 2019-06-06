<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\Exception\InvalidPointException;

/**
 * The point factory interface.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PointFactoryInterface
{
    /**
     * Creates a point of a certain type which is defined from the passed point FQN.
     *
     * @param string $pointFqn
     * @return PropertyPoint|MethodPoint|ParameterPoint
     * @throws InvalidPointException
     */
    public function createPoint(string $pointFqn): object;
}
