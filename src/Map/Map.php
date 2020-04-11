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

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;

/**
 * The map.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <opportus@gmail.com>
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
    public function __construct(PathFindingStrategyInterface $pathFindingStrategy, ?RouteCollection $routes = null)
    {
        $this->pathFindingStrategy = $pathFindingStrategy;
        $this->routes = $routes ?? new RouteCollection();
    }

    /**
     * Gets the routes connecting the points of the source with the points of the target.
     *
     * @param Context $context
     * @return RouteCollection
     */
    public function getRoutes(Context $context): RouteCollection
    {
        $routes = $this->pathFindingStrategy->getRoutes($context)->toArray();

        foreach ($this->routes as $route) {
            if ($context->getSourceClassFqn() === $route->getSourcePoint()->getClassFqn() &&
                $context->getTargetClassFqn() === $route->getTargetPoint()->getClassFqn()
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
