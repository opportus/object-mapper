<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;

/**
 * The map builder interface.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
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
     * @param null|CheckPointCollection $checkPoints
     *
     * A check point, added to a route, allows you to control (or transform) the value from the source point before it reaches the target point.
     * You can add multiple *check points* to a route. In this case, these *check points* form a chain.
     * The first *check point* controls the original value from the *source point* and returns the value (transformed or not) to the object mapper.
     * Then, the object mapper passes the value to the next checkpoint and so on...
     * Until the last checkpoint returns the final value to be assigned to the *target point* by the object mapper.
     * So it is important to keep in mind that each *check point* has an unique position (priority) on a route.
     * The routed value goes through each of the *check points* from the lowest to the highest positioned ones.
     *
     * @return MapBuilderInterface
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn, ?CheckPointCollection $checkPoints = null): MapBuilderInterface;

    /**
     * Builds the map.
     *
     * @param bool|PathFindingStrategyInterface $pathFindingStrategy
     *
     * - If `$pathFindingStrategy` is `false`, this method will build a map with `NoPathFindingStrategy`.
     * - If `$pathFindingStrategy` is `true`, this method will build a map with `PathFindingStrategy`.
     * - If `$pathFindingStrategy` is an instance of `PathFindingStrategyInterface`, this method will build a map with this instance.
     *
     * @return Map
     *
     * @throws InvalidArgumentException
     */
    public function buildMap($pathFindingStrategy = false): Map;
}
