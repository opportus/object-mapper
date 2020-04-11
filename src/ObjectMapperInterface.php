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

use Opportus\ObjectMapper\Map\Map;
use Opportus\ObjectMapper\Map\MapBuilderInterface;

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
     * Maps source points values to target points following the routes on the map.
     *
     * @param object $source
     * @param object|string $target
     * @param null|Map $map
     * @return null|object
     */
    public function map(object $source, $target, ?Map $map = null): ?object;
}
