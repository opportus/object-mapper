<?php

namespace Opportus\ObjectMapper\Map\Definition;

use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;

/**
 * The map definition interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapDefinitionInterface
{
    /**
     * Gets the ID of the map definition.
     *
     * @return string
     */
    public function getId() : string;

    /**
     * Gets the route collection of the map definition.
     *
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     */
    public function getRouteCollection() : RouteCollectionInterface;
}

