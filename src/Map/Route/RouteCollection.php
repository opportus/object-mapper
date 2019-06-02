<?php

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\AbstractImmutableCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The route collection.
 *
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class RouteCollection extends AbstractImmutableCollection
{
    /**
     * Constructs the route collection.
     *
     * @param Opportus\ObjectMapper\Map\Route\Route[] $routes
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function __construct(array $routes = [])
    {
        $indexedRoutes = [];

        foreach ($routes as $route) {
            if (!\is_object($route) || !$route instanceof Route) {
                throw new InvalidArgumentException(\sprintf(
                    'Argument "routes" passed to "%s" is invalid. Expects the array to contain elements of type "%s". Got an element of type "%s".',
                    __METHOD__,
                    Route::class,
                    \is_object($route) ? \get_class($route) : \gettype($route)
                ));
            }

            $indexedRoutes[$route->getFqn()] = $route;
        }

        parent::__construct($indexedRoutes);
    }
}
