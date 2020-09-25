<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\PathFinder\PathFinderCollection;
use Opportus\ObjectMapper\Point\StaticSourcePointInterface;
use Opportus\ObjectMapper\Point\StaticTargetPointInterface;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;

/**
 * The map.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class Map
{
    /**
     * @var PathFinderCollection $pathFinders
     */
    private $pathFinders;

    /**
     * @var RouteCollection $routes
     */
    private $routes;

    /**
     * Constructs the map.
     *
     * @param null|PathFinderCollection $pathFinders
     * @param null|RouteCollection $routes
     */
    public function __construct(
        ?PathFinderCollection $pathFinders,
        ?RouteCollection $routes = null
    ) {
        $this->pathFinders = $pathFinders ?? new PathFinderCollection();
        $this->routes = $routes ?? new RouteCollection();
    }

    /**
     * Gets the routes connecting the source points with the target points.
     *
     * Combines manually/statically added routes with automatically/dynamically
     * added routes. In case of duplicate between manually and automatically
     * added routes, manually added routes take precedence over automatically
     * added routes.
     *
     * @param Source $source
     * @param Target $target
     * @return RouteCollection
     */
    public function getRoutes(Source $source, Target $target): RouteCollection
    {
        $routes = [];

        foreach ($this->pathFinders as $pathFinder) {
            $routes = \array_merge(
                $routes,
                $pathFinder->getRoutes($source, $target)->toArray()
            );
        }

        foreach ($this->routes as $route) {
            if ($route->getSourcePoint() instanceof StaticSourcePointInterface &&
                false === $source->hasStaticPoint($route->getSourcePoint())
            ) {
                continue;
            }

            if ($route->getTargetPoint() instanceof StaticTargetPointInterface &&
                false === $target->hasStaticPoint($route->getTargetPoint())
            ) {
                continue;
            }

            $routes[$route->getFqn()] = $route;
        }

        return new RouteCollection($routes);
    }
}
