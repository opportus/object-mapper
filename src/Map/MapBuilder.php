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

use Opportus\ObjectMapper\PathFinder\DynamicSourceToStaticTargetPathFinder;
use Opportus\ObjectMapper\PathFinder\StaticSourceToDynamicTargetPathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinderCollection;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Route\RouteInterface;

/**
 * The map builder.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapBuilder implements MapBuilderInterface
{
    /**
     * @var RouteBuilderInterface $routeBuilder
     */
    private $routeBuilder;

    /**
     * @var RouteCollection $routes
     */
    private $routes;

    /**
     * @var PathFinderCollection $pathFinders
     */
    private $pathFinders;

    /**
     * Constructs the map builder.
     *
     * @param RouteBuilderInterface $routeBuilder
     * @param null|RouteCollection $routes
     * @param null|PathFinderCollection $pathFinders
     */
    public function __construct(
        RouteBuilderInterface $routeBuilder,
        ?RouteCollection $routes = null,
        ?PathFinderCollection $pathFinders = null
    ) {
        $this->routeBuilder = $routeBuilder;
        $this->routes = $routes ?? new RouteCollection();
        $this->pathFinders = $pathFinders ?? new PathFinderCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteBuilder(): RouteBuilderInterface
    {
        return $this->routeBuilder->setMapBuilder($this);
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(RouteInterface $route): MapBuilderInterface
    {
        $routes = $this->routes->toArray();

        $routes[] = $route;

        return new self(
            $this->routeBuilder,
            new RouteCollection($routes),
            $this->pathFinders
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addRoutes(RouteCollection $routes): MapBuilderInterface
    {
        $routes = $routes->toArray() + $this->routes->toArray();

        return new self(
            $this->routeBuilder,
            new RouteCollection($routes),
            $this->pathFinders
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addPathFinder(
        PathFinderInterface $pathFinder,
        ?int $priority = null
    ): MapBuilderInterface {
        $pathFinders = $this->pathFinders->toArray();

        if (null === $priority) {
            $pathFinders[] = $pathFinder;
        } else {
            $pathFinders[$priority] = $pathFinder;
        }

        return new self(
            $this->routeBuilder,
            $this->routes,
            new PathFinderCollection($pathFinders)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addStaticPathFinder(
        ?int $priority = null
    ): MapBuilderInterface {
        return $this->addPathFinder(
            new StaticPathFinder($this->routeBuilder),
            $priority
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addStaticSourceToDynamicTargetPathFinder(
        ?int $priority = null
    ): MapBuilderInterface {
        return $this->addPathFinder(
            new StaticSourceToDynamicTargetPathFinder($this->routeBuilder),
            $priority
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addDynamicSourceToStaticTargetPathFinder(
        ?int $priority = null
    ): MapBuilderInterface {
        return $this->addPathFinder(
            new DynamicSourceToStaticTargetPathFinder($this->routeBuilder),
            $priority
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMap(): MapInterface
    {
        return new Map($this->pathFinders, $this->routes);
    }
}
