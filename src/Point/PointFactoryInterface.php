<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The point factory interface.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PointFactoryInterface
{
    /**
     * Creates an object point of a certain type based on the passed object
     * point FQN.
     *
     * @param string $objectPointFqn
     * @return ObjectPoint
     * @throws InvalidArgumentException
     */
    public function createObjectPoint(string $objectPointFqn): ObjectPoint;
}
