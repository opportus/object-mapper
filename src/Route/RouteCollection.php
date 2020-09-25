<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\ImmutableCollection;

/**
 * The route collection.
 *
 * @package Opportus\ObjectMapper\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class RouteCollection extends ImmutableCollection
{
    /**
     * Constructs the route collection.
     *
     * @param RouteInterface[] $routes
     * @throws InvalidArgumentException
     */
    public function __construct(array $routes = [])
    {
        $indexedRoutes = [];

        foreach ($routes as $route) {
            if (!\is_object($route) || !$route instanceof RouteInterface) {
                $message = \sprintf(
                    'The array must contain exclusively elements of type %s, got an element of type %s.',
                    RouteInterface::class,
                    \is_object($route) ? \get_class($route) : \gettype($route)
                );

                throw new InvalidArgumentException(1, __METHOD__, $message);
            }

            $indexedRoutes[$route->getFqn()] = $route;
        }

        parent::__construct($indexedRoutes);
    }
}
