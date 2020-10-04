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
use Opportus\ObjectMapper\Point\DynamicSourcePointInterface;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\StaticSourcePointInterface;
use ReflectionClass;
use ReflectionObject;

/**
 * The source interface.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface SourceInterface
{
    /**
     * Gets the source Fully Qualified Name.
     *
     * @return string The Fully Qualified Class Name of the source
     */
    public function getFqn(): string;

    /**
     * Gets the source class reflection.
     *
     * @return ReflectionClass The class reflection of the source
     */
    public function getClassReflection(): ReflectionClass;

    /**
     * Gets the source object reflection.
     *
     * @return ReflectionObject The object reflection of the source
     */
    public function getObjectReflection(): ReflectionObject;

    /**
     * Gets the source instance.
     *
     * @return object The source instance
     */
    public function getInstance(): object;

    /**
     * Checks whether the source has the passed static point.
     *
     * @param  StaticSourcePointInterface $point A static source point
     * @return bool                              TRUE if the source class has
     *                                           this point statically defined
     *                                           or FALSE otherwise
     */
    public function hasStaticPoint(StaticSourcePointInterface $point): bool;

    /**
     * Checks whether the source has the passed dynamic point.
     *
     * @param  DynamicSourcePointInterface $point A dynamic source point
     * @return bool                               TRUE if the source object has
     *                                            this point dynamically defined
     *                                            or FALSE otherwise
     */
    public function hasDynamicPoint(DynamicSourcePointInterface $point): bool;

    /**
     * Gets the value of the passed source point.
     *
     * @param  SourcePointInterface      $point The source point to get the
     *                                          value from
     * @return mixed                            The value of the source point
     * @throws InvalidArgumentException         If the source point is static
     *                                          and the source class has no
     *                                          such point defined
     * @throws InvalidOperationException        If the operation fails for any
     *                                          reason
     */
    public function getPointValue(SourcePointInterface $point);
}
