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
use Opportus\ObjectMapper\Map\MapInterface;

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
     * Transfers data of the source to the target following routes on the map.
     *
     * @param object                     $source The object to map data from
     * @param object|string              $target The object (or the Fully
     *                                           Qualified Name of the class to
     *                                           instantiate and) to map data to
     * @param null|MapInterface          $map    An instance of `MapInterface`
     *                                           or NULL
     * @return null|object                       The instantiated and/or updated
     *                                           target or NULL if the there is
     *                                           no route mapping source and
     *                                           target
     * @throws InvalidArgumentException          If source or target arguments
     *                                           are invalid
     * @throws InvalidOperationException         If the operation fails for any
     *                                           reason
     */
    public function map(
        $source,
        $target,
        ?MapInterface $map = null
    );
}
