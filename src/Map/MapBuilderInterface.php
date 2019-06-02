<?php

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\Map\Filter\FilterInterface;

/**
 * The map builder interface.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface MapBuilderInterface
{
    /**
     * Adds a route to the map.
     *
     * @param string $sourcePointFqn Can be either:
     *
     * - A public, protected or private property (`PropertyPoint`) represented by its Fully Qualified Name having for syntax `My\Class::$property`
     * - A public, protected or private method requiring no argument (`MethodPoint`) represented by its Fully Qualified Name having for syntax `My\Class::method()`
     *
     * @param string $targetPointFqn Can be either:
     *
     * - A public, protected or private property (`PropertyPoint`) represented by its Fully Qualified Name having for syntax `My\Class::$property`
     * - A parameter of a public, protected or private method (`ParameterPoint`) represented by its Fully Qualified Name having for syntax `My\Class::method()::$parameter`
     *
     * @param null|Callable|Opportus\ObjectMapper\Map\Filter\FilterInterface $filter
     *
     * The callable returns a mixed value which will be assigned to the target point by the mapper. The callable takes as arguments:
     *
     * - `Opportus\ObjectMapper\Map\Route\Route` The route the filter is on.
     * - `Opportus\ObjectMapper\Context` The context of the current mapping.
     * - `Opportus\ObjectMapper\ObjectMapperInterface` The object mapper service, useful for recursion.
     *
     * @return Opportus\ObjectMapper\Map\MapBuilderInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn, $filter = null): MapBuilderInterface;

    /**
     * Adds a filter.
     *
     * @param Opportus\ObjectMapper\Map\Filter\FilterInterface $filter
     * @return Opportus\ObjectMapper\Map\MapBuilderInterface
     */
    public function addFilter(FilterInterface $filter): MapBuilderInterface;

    /**
     * Builds the map.
     *
     * @param bool|Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface $pathFindingStrategy
     *
     * - If `$pathFindingStrategy` is `false`, this will build a map with `NoPathFindingStrategy` returning as routes those previously manually added via this builder.
     * - If `$pathFindingStrategy` is `true`, this will build a map with `PathFindingStrategy` returning as routes those dynamically defined by this strategy.
     * - If `$pathFindingStrategy` is an instance of `PathFindingStrategyInterface`, this will build a map with this instance returning as routes those dynamically defined by this strategy.
     *
     * @return Opportus\ObjectMapper\Map\Map
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function buildMap($pathFindingStrategy = false): Map;
}
