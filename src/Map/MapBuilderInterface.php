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
     * @param string $sourcePointFqn
     * @param string $targetPointFqn
     * @param null|CheckPointCollection $checkPoints
     * @return MapBuilderInterface
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn, ?CheckPointCollection $checkPoints = null): MapBuilderInterface;

    /**
     * Builds the map.
     *
     * @param bool|PathFindingStrategyInterface $pathFindingStrategy
     * @return Map
     * @throws InvalidArgumentException
     */
    public function buildMap($pathFindingStrategy = false): Map;
}
