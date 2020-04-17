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

use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;

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
     * @var PathFindingStrategyInterface $pathFindingStrategy
     */
    private $pathFindingStrategy;

    /**
     * @var RouteCollection $routes
     */
    private $routes;

    /**
     * Constructs the map.
     *
     * @param PathFindingStrategyInterface $pathFindingStrategy
     * @param null|RouteCollection $routes
     */
    public function __construct(
        PathFindingStrategyInterface $pathFindingStrategy,
        ?RouteCollection $routes = null
    ) {
        $this->pathFindingStrategy = $pathFindingStrategy;

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
        $routes = $this->pathFindingStrategy
            ->getRoutes($source, $target)->toArray();

        foreach ($this->routes as $route) {
            if (
                $source->hasPoint($route->getSourcePoint()) &&
                $target->hasPoint($route->getTargetPoint())
            ) {
                $routes[] = $route;
            }
        }

        return new RouteCollection($routes);
    }

    /**
     * Gets the Fully Qualified Name of the path finding strategy.
     *
     * @return string
     */
    public function getPathFindingStrategyFqn(): string
    {
        return \get_class($this->pathFindingStrategy);
    }
}
