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
     * @param  string                     $pointFqn The Fully Qualified Name of
     *                                              a static source point to
     *                                              create
     * @return StaticSourcePointInterface           The static source point
     *                                              created
     * @throws InvalidArgumentException             If the first argument does
     *                                              not match the FQN regex
     *                                              pattern of any static source
     *                                              point type
     */
    public function createStaticSourcePoint(string $pointFqn): StaticSourcePointInterface;

    /**
     * Creates a static target point of a certain type based
     * on the passed FQN.
     *
     * @param  string                     $pointFqn The Fully Qualified Name of
     *                                              a static target point to
     *                                              create
     * @return StaticTargetPointInterface           The static target point
     *                                              created
     * @throws InvalidArgumentException             If the first argument does
     *                                              not match the FQN regex
     *                                              pattern of any static target
     *                                              point type
     */
    public function createStaticTargetPoint(string $pointFqn): StaticTargetPointInterface;

    /**
     * Creates a dynamic source point of a certain type based
     * on the passed FQN.
     *
     * @param  string                      $pointFqn The Fully Qualified Name of
     *                                               a dynamic source point to
     *                                               create
     * @return DynamicSourcePointInterface           The dynamic source point
     *                                               created
     * @throws InvalidArgumentException              If the first argument does
     *                                               not match the FQN regex
     *                                               pattern of any dynamic
     *                                               source point type
     */
    public function createDynamicSourcePoint(string $pointFqn): DynamicSourcePointInterface;

    /**
     * Creates a dynamic target point of a certain type based
     * on the passed FQN.
     *
     * @param  string                      $pointFqn The Fully Qualified Name of
     *                                               a dynamic target point to
     *                                               create
     * @return DynamicTargetPointInterface           The dynamic target point
     *                                               created
     * @throws InvalidArgumentException              If the first argument does
     *                                               not match the FQN regex
     *                                               pattern of any dynamic
     *                                               target point type
     */
    public function createDynamicTargetPoint(string $pointFqn): DynamicTargetPointInterface;
}
