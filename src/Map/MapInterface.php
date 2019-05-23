<?php

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;

/**
 * The map interface.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapInterface
{
    /**
     * Gets a collection of routes connecting the points of the source with the points of the target.
     *
     * @param object $source
     * @param object|string $target
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     */
    public function getRouteCollection(object $source, $target): RouteCollectionInterface;

    /**
     * Gets the type of the path finding strategy.
     *
     * @return string
     */
    public function getPathFindingStrategyType(): string;
}
