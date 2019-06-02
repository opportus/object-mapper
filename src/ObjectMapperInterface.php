<?php

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Map\Map;
use Opportus\ObjectMapper\Map\MapBuilderInterface;

/**
 * The object mapper interface.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface ObjectMapperInterface
{
    /**
     * Gets the map builder.
     *
     * @return Opportus\ObjectMapper\Map\MapBuilderInterface
     */
    public function getMapBuilder(): MapBuilderInterface;

    /**
     * Maps source points values to target points following the routes on the map.
     *
     * @param object $source
     * @param object|string $target
     * @param null|Opportus\ObjectMapper\Map\Map $map
     * @return null|object
     */
    public function map(object $source, $target, ?Map $map = null): ?object;
}
