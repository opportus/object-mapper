<?php

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface;
use Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface;

/**
 * The route interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface RouteInterface
{
    /**
     * Gets the Fully Qualified Name of the route.
     *
     * @return string
     */
    public function getFqn() : string;

    /**
     * Get the source point of the route.
     *
     * @return Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface
     */
    public function getSourcePoint() : SourcePointInterface;

    /**
     * Get the target point of the route.
     *
     * @return Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface
     */
    public function getTargetPoint() : TargetPointInterface;
}

