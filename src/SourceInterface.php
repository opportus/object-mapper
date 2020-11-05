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
use Opportus\ObjectMapper\Point\SourcePointInterface;
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
     * Gets the value of the passed source point.
     *
     * @param  SourcePointInterface      $point The source point to get the
     *                                          value from
     * @return mixed                            The value of the source point
     * @throws InvalidArgumentException         If the source FQN of the point
     *                                          does not match the source FQN or
     *                                          if the point type is not
     *                                          supported
     * @throws InvalidOperationException        If the operation fails for any
     *                                          reason
     */
    public function getPointValue(SourcePointInterface $point);
}
