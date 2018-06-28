<?php

namespace Opportus\ObjectMapper\Map\Definition;

use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;

/**
 * The map definition preparation interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 * @internal
 */
interface MapDefinitionPreparationInterface
{
    /**
     * Sets the ID of the map definition.
     *
     * @param  string $id
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionPreparationInterface
     */
    public function setId(string $id) : MapDefinitionPreparationInterface;

    /**
     * Gets the ID of the map definition.
     *
     * @return null|string
     */
    public function getId() : ?string;

    /**
     * Sets the route collection of the map definition.
     *
     * @param  Opportus\ObjectMapper\Map\Route\RouteCollectionInterface $routeCollection
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionPreparationInterface
     */
    public function setRouteCollection(RouteCollectionInterface $routeCollection) : MapDefinitionPreparationInterface;

    /**
     * Gets the route collection of the map definition.
     *
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     */
    public function getRouteCollection() : RouteCollectionInterface;
}

