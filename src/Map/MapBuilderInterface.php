<?php

namespace Opportus\ObjectMapper\Map;

/**
 * The map builder interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapBuilderInterface
{
    /**
     * Prepares a new map.
     *
     * @return Opportus\ObjectMapper\Map\MapBuilderInterface
     */
    public function prepareMap() : MapBuilderInterface;

    /**
     * Adds a route to the map.
     *
     * @param  string $sourcePointFqn Representing the Fully Qualified Name of a source point which can be:
     *
     * - A public, protected or private property (PropertyPoint) represented by its FQN having for syntax 'Class::$property'
     * - A public, protected or private method requiring no argument (MethodPoint) represented by its FQN having for syntax 'Class::method()'
     *
     * @param  string $targetPointFqn Representing the Fully Qualified Name of a target point which can be:
     *
     * - A public, protected or private property (PropertyPoint) represented by its FQN having for syntax 'Class::$property'
     * - A parameter of a public, protected or private method (ParameterPoint) represented by its FQN having for syntax 'Class::method()::$parameter'
     *
     * @return Opportus\ObjectMapper\Map\MapBuilderInterface
     *
     * @throws Opportus\ObjectMapper\Exception\InvalidOperationException When the client has not previously called MapBuilderInterface::prepareMap()
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn) : MapBuilderInterface;

    /**
     * Builds the map.
     *
     * @param  null|Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface|Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface
     *
     * - If the param is null and a map is in preparation, it will build the prepared map
     * - If the param is null and no map is in preparation, it will build a map having for stratgey the default PathFindingStrategy
     * - If the param is an instance of PathFindingStrategyInterface and no map is in preparation, it will build a map having for strategy this instance
     * - If the param is an instance of MapDefinitionInterface and no map is in preparation, it will build a map based on this map definition instance
     *
     * @return Opportus\ObjectMapper\Map\MapInterface
     *
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException When the param !== null and a map is already in preparation
     */
    public function buildMap($parameter = null) : MapInterface;
}

