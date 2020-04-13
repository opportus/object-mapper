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

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
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
     * @throws InvalidOperationException
     */
    public function __construct(
        PathFindingStrategyInterface $pathFindingStrategy,
        ?RouteCollection $routes = null
    ) {
        $this->pathFindingStrategy = $pathFindingStrategy;

        try {
            $this->routes = $routes ?? new RouteCollection();
        } catch (InvalidArgumentException $exception) {
            throw new InvalidOperationException(\sprintf(
                'Invalid "%s" operation. %s',
                __METHOD__,
                $exception->getMessage()
            ));
        }
    }

    /**
     * Gets the routes connecting the source points with the target points.
     *
     * @param Source $source
     * @param Target $target
     * @return RouteCollection
     * @throws InvalidOperationException
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

        try {
            return new RouteCollection($routes);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidOperationException(\sprintf(
                'Invalid "%s" operation. %s',
                __METHOD__,
                $exception->getMessage()
            ));
        }
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
