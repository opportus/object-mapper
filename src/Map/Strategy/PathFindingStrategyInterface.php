<?php

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\RouteCollection;

/**
 * The path finding strategy interface.
 *
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PathFindingStrategyInterface
{
    /**
     * Gets the routes connecting the points of the source with the points of the target.
     *
     * @param Opportus\ObjectMapper\Context $context
     * @return Opportus\ObjectMapper\Map\Route\RouteCollection
     */
    public function getRoutes(Context $context): RouteCollection;
}
