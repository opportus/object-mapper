<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\DynamicTargetPointInterface;
use Opportus\ObjectMapper\Point\StaticTargetPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use ReflectionClass;
use ReflectionObject;

/**
 * The target interface.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface TargetInterface
{
    /**
     * Gets the target Fully Qualified Name.
     *
     * @return string The Fully Qualified Class Name of the target
     */
    public function getFqn(): string;

    /**
     * Gets the target class reflection.
     *
     * @return ReflectionClass The class reflection of the target
     */
    public function getClassReflection(): ReflectionClass;

    /**
     * Gets the target object reflection.
     *
     * @return null|ReflectionObject The object reflection of the target
     */
    public function getObjectReflection(): ?ReflectionObject;

    /**
     * Gets the target instance.
     *
     * @return null|object The target instance if it has been instantiated
     *                     or null otherwise
     */
    public function getInstance(): ?object;

    /**
     * Checks whether the target has the passed static point.
     *
     * @param  StaticTargetPointInterface $point A static target point
     * @return bool                              TRUE if the target class has
     *                                           this point statically defined
     *                                           or FALSE otherwise
     */
    public function hasStaticPoint(StaticTargetPointInterface $point): bool;

    /**
     * Checks whether the target has the passed dynamic point.
     *
     * @param  DynamicTargetPointInterface $point A dynamic target point
     * @return bool                               TRUE if the target object has
     *                                            this point dynamically defined
     *                                            or FALSE otherwise
     */
    public function hasDynamicPoint(DynamicTargetPointInterface $point): bool;

    /**
     * Sets the value of the passed target point.
     *
     * @param  TargetPointInterface      $point      The target point to assign
     *                                               the value to
     * @param  mixed                     $pointValue The value to assign to the
     *                                               target
     * @throws InvalidArgumentException              If the target point is
     *                                               static and the target class
     *                                               has no such point defined
     * @throws InvalidOperationException             If the operation fails for
     *                                               any reason
     */
    public function setPointValue(TargetPointInterface $point, $pointValue);

    /**
     * Operates the target, effectively assigning the previously set
     * values (with `TargetInterface::setPointValue()`) to their points.
     *
     * @throws InvalidOperationException If the operation fails for any reason
     */
    public function operate();

    /**
     * Checks whether the target is operated.
     *
     * @return bool TRUE if the target is operated or FALSE otherwise
     */
    public function isOperated(): bool;

    /**
     * Checks whether the target is instantiated.
     *
     * @return bool TRUE if the target is instantiated or FALSE otherwise
     */
    public function isInstantiated(): bool;
}
