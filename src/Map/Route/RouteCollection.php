<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\AbstractImmutableCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The route collection.
 *
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class RouteCollection extends AbstractImmutableCollection
{
    /**
     * Constructs the route collection.
     *
     * @param Route[] $routes
     * @throws InvalidArgumentException
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
