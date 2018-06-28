<?php

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\Map\MapBuilderInterface;

/**
 * The object mapper interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface ObjectMapperInterface
{
    /**
     * Gets the map builder service.
     *
     * @return Opportus\ObjectMapper\Map\MapBuilderInterface
     */
    public function getMapBuilder() : MapBuilderInterface;

    /**
     * Maps source point values to target points following the routes given by the map.
     *
     * @param object|array $sources Can be:
     *
     * - An object holding data to map to the target(s)
     * - An array of objects holding data to map to the target(s)
     *
     * @param object|array|string $targets Can be:
     *
     * - An object to map the source(s) data to
     * - A string being the class name of an object to instantiate and to map the source(s) data to
     * - An array of single or both type of element above
     *
     * @param null|Opportus\ObjectMapper\Map\MapInterface $map
     *
     * @return object|array The return value depends on the type of the $targets parameter and on the $map parameter:
     *
     * - If $targets is an object, the very same object will be returned
     * - If $targets is a class name, an object of this type will be returned
     * - If $targets is an array of single or both type of element above, the same array of both type of return value above will be returned
     * - If $map does not contain any route to a target element, the target element will be returned as it has been passed
     *
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function map($sources, $targets, ?MapInterface $map = null);
}

