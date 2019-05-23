<?php

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Map\MapInterface;
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
    public function getMapBuilder() : MapBuilderInterface;

    /**
     * Maps source points values to target points following the routes from the map.
     *
     * @param object $source
     * @param object|string $target
     * @param null|Opportus\ObjectMapper\Map\MapInterface $map
     * @return null|object
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function map(object $source, $target, ?MapInterface $map = null): ?object;
}
