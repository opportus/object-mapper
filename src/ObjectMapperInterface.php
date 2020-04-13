<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Map;

/**
 * The object mapper interface.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface ObjectMapperInterface
{
    /**
     * Maps source points values to target points following routes on the map.
     *
     * @param object $source The instance to map data from
     * @param object|string $target The instance (or the Fully Qualified Name of
     *                              the class to instantiate and) to map data to
     * @param null|Map $map The instance of Map. If it is null,
     *                      the method builds and uses a map composed of
     *                      the default `PathFindingStrategy`
     * @return null|object The instantiated and/or updated target or null if
     *                     the map has no route
     * @throws InvalidArgumentException
     * @throws InvalidOperationException
     */
    public function map(object $source, $target, ?Map $map = null): ?object;
}
