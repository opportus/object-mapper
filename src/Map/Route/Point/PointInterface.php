<?php

namespace Opportus\ObjectMapper\Map\Route\Point;

/**
 * The point interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PointInterface
{
    /**
     * Gets the Fully Qualified Name of the point.
     *
     * @return string
     */
    public function getFqn() : string;

    /**
     * Gets the Fully Qualified Name of the class of the point.
     *
     * @return string
     */
    public function getClassFqn() : string;
}

