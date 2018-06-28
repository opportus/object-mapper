<?php

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;

/**
 * The map interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapInterface
{
    /**
     * Gets the route collection connecting the points of the source class with the points of the target class.
     *
     * @param  string $sourceClassFqn
     * @param  string $targetClassFqn
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     */
    public function getRouteCollection(string $sourceClassFqn, string $targetClassFqn) : RouteCollectionInterface;

    /**
     * Gets the type of the path finding strategy.
     *
     * @return string
     */
    public function getPathFindingStrategyType() : string;
}

