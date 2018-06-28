<?php

namespace Opportus\ObjectMapper\Map\Definition;

/**
 * The map definition registry interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapDefinitionRegistryInterface
{
    /**
     * Gets the map definition from the registry.
     *
     * @param  string $mapDefinitionId
     * @return null|Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface
     */
    public function getMapDefinition(string $mapDefinitionId) : ?MapDefinitionInterface;

    /**
     * Adds the map definition to the registry.
     *
     * @param  Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface $mapDefinition
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionRegistryInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidOperationException When the registry already contains another map definition having an identical ID
     */
    public function addMapDefinition(MapDefinitionInterface $mapDefinition) : MapDefinitionRegistryInterface;

    /**
     * Removes the map definition from the registry.
     *
     * @param  string $mapDefinitionId
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionRegistryInterface
     */
    public function removeMapDefinition(string $mapDefinitionId) : MapDefinitionRegistryInterface;

    /**
     * Checks whether the registry has the map definition.
     *
     * @param  string $mapDefinitionId
     * @return bool
     */
    public function hasMapDefinition(string $mapDefinitionId) : bool;
}

