<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
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
     * Creates a static source point of a certain type based on the passed FQN.
     *
     * @param  string                     $pointFqn The Fully Qualified Name of
     *                                              a static source point to
     *                                              create
     * @return StaticSourcePointInterface           The static source point
     *                                              created
     * @throws InvalidArgumentException             If the first argument does
     *                                              not match the FQN regex
     *                                              pattern of any static source
     *                                              point type or if the first
     *                                              argument is invalid for any
     *                                              other reason
     */
    public function createStaticSourcePoint(
        string $pointFqn
    ): StaticSourcePointInterface;

    /**
     * Creates a static target point of a certain type based on the passed FQN.
     *
     * @param  string                     $pointFqn The Fully Qualified Name of
     *                                              a static target point to
     *                                              create
     * @return StaticTargetPointInterface           The static target point
     *                                              created
     * @throws InvalidArgumentException             If the first argument does
     *                                              not match the FQN regex
     *                                              pattern of any static target
     *                                              point type or if the first
     *                                              argument is invalid for any
     *                                              other reason
     */
    public function createStaticTargetPoint(
        string $pointFqn
    ): StaticTargetPointInterface;

    /**
     * Creates a dynamic source point of a certain type based on the passed FQN.
     *
     * @param  string                      $pointFqn The Fully Qualified Name of
     *                                               a dynamic source point to
     *                                               create
     * @return DynamicSourcePointInterface           The dynamic source point
     *                                               created
     * @throws InvalidArgumentException              If the first argument does
     *                                               not match the FQN regex
     *                                               pattern of any dynamic
     *                                               source point type or if the
     *                                               first argument is invalid
     *                                               for any other reason
     */
    public function createDynamicSourcePoint(
        string $pointFqn
    ): DynamicSourcePointInterface;

    /**
     * Creates a dynamic target point of a certain type based on the passed FQN.
     *
     * @param  string                      $pointFqn The Fully Qualified Name of
     *                                               a dynamic target point to
     *                                               create
     * @return DynamicTargetPointInterface           The dynamic target point
     *                                               created
     * @throws InvalidArgumentException              If the first argument does
     *                                               not match the FQN regex
     *                                               pattern of any dynamic
     *                                               target point type or if the
     *                                               first argument is invalid
     *                                               for any other reason
     */
    public function createDynamicTargetPoint(
        string $pointFqn
    ): DynamicTargetPointInterface;

    /**
     * Creates a source point of a certain type based on the passed FQN.
     *
     * @param  string                   $pointFqn The Fully Qualified Name of a
     *                                            source point to create
     * @return SourcePointInterface               The source point created
     * @throws InvalidArgumentException           If the first argument does
     *                                            not match the FQN regex
     *                                            pattern of any source point
     *                                            type or if the first argument
     *                                            is invalid for any other
     *                                            reason
     */
    public function createSourcePoint(
        string $pointFqn
    ): SourcePointInterface;

    /**
     * Creates a target point of a certain type based on the passed FQN.
     *
     * @param  string                   $pointFqn The Fully Qualified Name of a
     *                                            target point to create
     * @return TargetPointInterface               The target point created
     * @throws InvalidArgumentException           If the first argument does
     *                                            not match the FQN regex
     *                                            pattern of any target point
     *                                            type or if the first argument
     *                                            is invalid for any other
     *                                            reason
     */
    public function createTargetPoint(
        string $pointFqn
    ): TargetPointInterface;

    /**
     * Creates a recursion check point.
     *
     * @param  string              $sourceFqn            The Fully Qualified
     *                                                   Name of the recursion
     *                                                   source to map data from
     * @param  string              $targetFqn            The Fully Qualified
     *                                                   Name of the recursion
     *                                                   target to map data to
     * @param  string              $targetSourcePointFqn The Fully Qualified
     *                                                   Name of the source
     *                                                   point to get recursion
     *                                                   target instance from
     * @return RecursionCheckPoint                       A recursion check point
     * @throws InvalidArgumentException                  If the first or second
     *                                                   argument is invalid for
     *                                                   any reason
     */
    public function createRecursionCheckPoint(
        string $sourceFqn,
        string $targetFqn,
        string $targetSourcePointFqn
    ): RecursionCheckPoint;

    /**
     * Creates a iterable recursion check point.
     *
     * @param  string              $sourceFqn            The Fully Qualified
     *                                                   Name of the iterable
     *                                                   recursion source to map
     *                                                   data from
     * @param  string              $targetFqn            The Fully Qualified
     *                                                   Name of the iterable
     *                                                   recursion target to map
     *                                                   data to
     * @param  string      $targetIterableSourcePointFqn The Fully Qualified
     *                                                   Name of the source
     *                                                   point to get the iterable
     *                                                   recursion target instance
     *                                                   from
     * @return IterableRecursionCheckPoint               A recursion check point
     * @throws InvalidArgumentException                  If the first or second
     *                                                   argument is invalid for
     *                                                   any reason
     */
    public function createIterableRecursionCheckPoint(
        string $sourceFqn,
        string $targetFqn,
        string $targetIterableSourcePointFqn
    ): IterableRecursionCheckPoint;
}
