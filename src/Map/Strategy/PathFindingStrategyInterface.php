<?php

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;

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
     * Gets a collection of routes connecting the points of the source with the points of the target.
     *
     * @param object $source
     * @param object|string $target
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function getRouteCollection(object $source, $target): RouteCollectionInterface;
}
