<?php

namespace Opportus\ObjectMapper\Map\Definition;

/**
 * The map definition collection interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapDefinitionCollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Gets the map definitions from the collection.
     *
     * @return array
     */
    public function getMapDefinitions() : array;

    /**
     * Gets a map definition from the collection.
     *
     * @param  string $mapDefinitionId
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface
     */
    public function getMapDefinition(string $mapDefinitionId) : MapDefinitionInterface;

    /**
     * Adds a map definition to the collection.
     *
     * @param  Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface $mapDefinition
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionCollectionInterface
     */
    public function addMapDefinition(MapDefinitionInterface $mapDefinition) : MapDefinitionCollectionInterface;

    /**
     * Adds the map definitions to the collection.
     *
     * @param  array $mapDefinitions
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionCollectionInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException When the param contains a type of element which is not an instance of MapDefinitionInterface
     */
    public function addMapDefinitions(array $mapDefinitions) : MapDefinitionCollectionInterface;

    /**
     * Removes a map definition from the collection.
     *
     * @param  string $mapDefinitionId
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionCollectionInterface
     */
    public function removeMapDefinition(string $mapDefinitionId) : MapDefinitionCollectionInterface;

    /**
     * Removes the map definitions from the collection.
     *
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionCollectionInterface
     */
    public function removeMapDefinitions() : MapDefinitionCollectionInterface;

    /**
     * Checks if the collection has a map definition.
     *
     * @param  string $mapDefinitionId
     * @return bool
     */
    public function hasMapDefinition(string $mapDefinitionId) : bool;

    /**
     * Checks if the collection has any map definition.
     *
     * @return bool
     */
    public function hasMapDefinitions() : bool;
}

