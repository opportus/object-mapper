<?php

namespace Opportus\ObjectMapper\Map\Definition;

/**
 * The map definition builder interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapDefinitionBuilderInterface
{
    /**
     * Prepares a new map definition.
     *
     * @param  null|Opportus\ObjectMapper\Map\Definition\MapDefinitionPreparationInterface $mapDefinitionPreparation
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface
     */
    public function prepareMapDefinition(?MapDefinitionPreparationInterface $mapDefinitionPreparation = null) : MapDefinitionBuilderInterface;

    /**
     * Sets the ID of the map definition.
     *
     * @param  string $id
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidOperationException When the client has not previously called MapDefinitionBuilderInterface::prepareMapDefinition()
     */
    public function setId(string $id) : MapDefinitionBuilderInterface;

    /**
     * Adds a route to the map definition.
     *
     * @param  string $sourcePointFqn
     * @param  string $targetPointFqn
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidOperationException When the client has not previously called MapDefinitionBuilderInterface::prepareMapDefinition()
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn) : MapDefinitionBuilderInterface;

    /**
     * Builds the prepared map definition.
     *
     * @return Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidOperationException When the client has not previously called MapDefinitionBuilderInterface::prepareMapDefinition()
     */
    public function buildMapDefinition() : MapDefinitionInterface;
}

