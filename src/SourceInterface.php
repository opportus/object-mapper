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
use Opportus\ObjectMapper\Point\StaticSourcePointInterface;
use ReflectionClass;

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
     * @return string
     */
    public function getFqn(): string;

    /**
     * Gets the source reflection.
     *
     * @return ReflectionClass
     */
    public function getReflection(): ReflectionClass;

    /**
     * Gets the source instance.
     *
     * @return object
     */
    public function getInstance(): object;

    /**
     * Checks whether the source has the passed static point.
     *
     * @param StaticSourcePointInterface $point
     * @return bool
     */
    public function hasStaticPoint(StaticSourcePointInterface $point): bool;

    /**
     * Gets the value of the passed source point.
     *
     * @param SourcePointInterface $point
     * @return mixed
     * @throws InvalidArgumentException
     * @throws InvalidOperationException
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function getPointValue(SourcePointInterface $point);
}
