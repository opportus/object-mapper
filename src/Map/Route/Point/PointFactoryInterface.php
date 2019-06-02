<?php

namespace Opportus\ObjectMapper\Map\Route\Point;

/**
 * The point factory interface.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PointFactoryInterface
{
    /**
     * Creates a point of a certain type which is defined from the passed point FQN.
     *
     * @param string $pointFqn
     * @return Opportus\ObjectMapper\Map\Route\Point\PropertyPoint|Opportus\ObjectMapper\Map\Route\Point\MethodPoint|Opportus\ObjectMapper\Map\Route\Point\ParameterPoint
     * @throws Opportus\ObjectMapper\Exception\InvalidPointException
     */
    public function createPoint(string $pointFqn): object;
}
