<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Filter\FilterInterface;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;

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
     * Adds a route.
     *
     * @param string $sourcePointFqn Can be either:
     *
     * - A public, protected or private property (`PropertyPoint`) represented by its Fully Qualified Name having for syntax `My\Class.$property`
     * - A public, protected or private method requiring no argument (`MethodPoint`) represented by its Fully Qualified Name having for syntax `My\Class.method()`
     *
     * @param string $targetPointFqn Can be either:
     *
     * - A public, protected or private property (`PropertyPoint`) represented by its Fully Qualified Name having for syntax `My\Class.$property`
     * - A parameter of a public, protected or private method (`ParameterPoint`) represented by its Fully Qualified Name having for syntax `My\Class.method().$parameter`
     *
     * @return MapBuilderInterface
     * @throws InvalidArgumentException
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn): MapBuilderInterface;

    /**
     * Adds a filter.
     *
     * @param FilterInterface $filter
     * @return MapBuilderInterface
     */
    public function addFilter(FilterInterface $filter): MapBuilderInterface;

    /**
     * Adds a filter on a specific route.
     *
     * @param Callable $callable
     *
     * The callable returns a mixed value which will be assigned to the target point by the mapper. The callable takes as arguments:
     *
     * - `Opportus\ObjectMapper\Map\Route\Route` The route the filter is on.
     * - `Opportus\ObjectMapper\Context` The context of the current mapping.
     * - `Opportus\ObjectMapper\ObjectMapperInterface` The object mapper service, useful for recursion.
     *
     * @param string $sourcePointFqn Can be either:
     *
     * - A public, protected or private property (`PropertyPoint`) represented by its Fully Qualified Name having for syntax `My\Class.$property`
     * - A public, protected or private method requiring no argument (`MethodPoint`) represented by its Fully Qualified Name having for syntax `My\Class.method()`
     *
     * @param string $targetPointFqn Can be either:
     *
     * - A public, protected or private property (`PropertyPoint`) represented by its Fully Qualified Name having for syntax `My\Class.$property`
     * - A parameter of a public, protected or private method (`ParameterPoint`) represented by its Fully Qualified Name having for syntax `My\Class.method().$parameter`
     *
     * @return MapBuilderInterface
     */
    public function addFilterOnRoute(callable $callable, string $sourcePointFqn, string $targetPointFqn): MapBuilderInterface;

    /**
     * Builds the map.
     *
     * @param bool|PathFindingStrategyInterface $pathFindingStrategy
     *
     * - If `$pathFindingStrategy` is `false`, this will build a map with `NoPathFindingStrategy` returning as routes those previously manually added via this builder.
     * - If `$pathFindingStrategy` is `true`, this will build a map with `PathFindingStrategy` returning as routes those dynamically defined by this strategy.
     * - If `$pathFindingStrategy` is an instance of `PathFindingStrategyInterface`, this will build a map with this instance returning as routes those dynamically defined by this strategy.
     *
     * @return Map
     * @throws InvalidArgumentException
     */
    public function buildMap($pathFindingStrategy = false): Map;
}
