<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map;

use Exception;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\PathFinder\PathFinderCollection;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;

/**
 * The map.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class Map implements MapInterface
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
        ?PathFinderCollection $pathFinders = null,
        ?RouteCollection $routes = null
    ) {
        $this->pathFinders = $pathFinders ?? new PathFinderCollection();
        $this->routes = $routes ?? new RouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes(SourceInterface $source, TargetInterface $target): RouteCollection
    {
        $routes = [];

        foreach ($this->pathFinders as $pathFinder) {
            try {
                $pathFinderRoutes = $pathFinder->getRoutes($source, $target);
            } catch (Exception $exception) {
                throw new InvalidOperationException('', 0, $exception);
            }

            foreach ($pathFinderRoutes as $pathFinderRoute) {
                $routes[] = $pathFinderRoute;
            }
        }

        foreach ($this->routes as $mapRoute) {
            $sourceFqn = $mapRoute->getSourcePoint()->getSourceFqn();
            $targetFqn = $mapRoute->getTargetPoint()->getTargetFqn();

            if ($sourceFqn !== $source->getFqn() || $targetFqn !== $target->getFqn()) {
                continue;
            }

            $routes[] = $mapRoute;
        }

        $routesToKeep = [];

        foreach ($routes as $key => $route) {
            $routesToKeep[$route->getTargetPoint()->getFqn()] = $key;
        }

        foreach ($routes as $key => $route) {
            if (false === \in_array($key, $routesToKeep)) {
                unset($routes[$key]);
            }
        }

        return new RouteCollection($routes);
    }
}
