<?php

namespace Opportus\ObjectMapper\Map\Route\Point;

/**
 * The source point interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface SourcePointInterface extends PointInterface
{
    /**
     * Gets the point value from the passed object.
     *
     * @param  null|object $object
     * @return mixed
     */
    public function getValue($object = null);
}

