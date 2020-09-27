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
use Opportus\ObjectMapper\Point\StaticTargetPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use ReflectionClass;

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
     * @return string
     */
    public function getFqn(): string;

    /**
     * Gets the target reflection.
     *
     * @return ReflectionClass
     */
    public function getReflection(): ReflectionClass;

    /**
     * Gets the target instance.
     *
     * @return null|object
     */
    public function getInstance(): ?object;

    /**
     * Checks whether the target has the passed static point.
     *
     * @param StaticTargetPointInterface $point
     * @return bool
     */
    public function hasStaticPoint(StaticTargetPointInterface $point): bool;

    /**
     * Sets the value of the passed target point.
     *
     * @param TargetPointInterface $point
     * @param mixed $pointValue
     * @throws InvalidArgumentException
     */
    public function setPointValue(TargetPointInterface $point, $pointValue);

    /**
     * Operates the instance.
     *
     * @throws InvalidOperationException
     */
    public function operateInstance();

    /**
     * Checks whether the target is operated.
     *
     * @return bool
     */
    public function isOperated(): bool;

    /**
     * Checks whether the target is instantiated.
     *
     * @return bool
     */
    public function isInstantiated(): bool;
}
