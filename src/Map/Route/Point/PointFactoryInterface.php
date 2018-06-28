<?php

namespace Opportus\ObjectMapper\Map\Route\Point;

/**
 * The point factory interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PointFactoryInterface
{
    /**
     * Creates a source point of a certain type which is defined from the passed point FQN.
     *
     * @param  string $pointFqn
     * @return Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface
     * @throws Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidPointException
     */
    public function createSourcePoint(string $pointFqn) : SourcePointInterface;

    /**
     * Creates a target point of a certain type which is defined from the passed point FQN.
     *
     * @param  string $pointFqn
     * @return Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface
     * @throws Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidPointException
     */
    public function createTargetPoint(string $pointFqn) : TargetPointInterface;
}

