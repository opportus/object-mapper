<?php

namespace Opportus\ObjectMapper\Map\Route;

/**
 * The route builder interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface RouteBuilderInterface
{
    /**
     * Builds a route.
     *
     * @param  string $sourcePointFqn
     * @param  string $targetPointFqn
     * @return Opportus\ObjectMapper\Map\Route\RouteInterface
     */
    public function buildRoute(string $sourcePointFqn, string $targetPointFqn) : RouteInterface;
}

