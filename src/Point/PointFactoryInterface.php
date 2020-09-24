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
     * Creates a static source point of a certain type based
     * on the passed FQN.
     *
     * @param string $pointFqn
     * @return StaticSourcePointInterface
     * @throws InvalidArgumentException
     */
    public function createStaticSourcePoint(string $pointFqn): StaticSourcePointInterface;

    /**
     * Creates a static target point of a certain type based
     * on the passed FQN.
     *
     * @param string $pointFqn
     * @return StaticTargetPointInterface
     * @throws InvalidArgumentException
     */
    public function createStaticTargetPoint(string $pointFqn): StaticTargetPointInterface;

    /**
     * Creates a dynamic source point of a certain type based
     * on the passed FQN.
     *
     * @param string $pointFqn
     * @return DynamicSourcePointInterface
     * @throws InvalidArgumentException
     */
    public function createDynamicSourcePoint(string $pointFqn): DynamicSourcePointInterface;

    /**
     * Creates a dynamic target point of a certain type based
     * on the passed FQN.
     *
     * @param string $pointFqn
     * @return DynamicTargetPointInterface
     * @throws InvalidArgumentException
     */
    public function createDynamicTargetPoint(string $pointFqn): DynamicTargetPointInterface;
}
