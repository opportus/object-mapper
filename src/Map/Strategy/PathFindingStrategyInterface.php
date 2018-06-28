<?php

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;

/**
 * The path finding strategy interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface PathFindingStrategyInterface
{
    /**
     * Gets a collection of routes connecting the points of the passed source class with the points of the passed target class.
     *
     * @param  string $sourceClassFqn
     * @param  string $targetClassFqn
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function getRouteCollection(string $sourceClassFqn, string $targetClassFqn) : RouteCollectionInterface;
}

