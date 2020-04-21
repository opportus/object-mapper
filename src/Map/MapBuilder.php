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
use Opportus\ObjectMapper\PathFinding\NoPathFinding;
use Opportus\ObjectMapper\PathFinding\PathFinding;
use Opportus\ObjectMapper\PathFinding\PathFindingInterface;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;

/**
 * The map builder.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class MapBuilder implements MapBuilderInterface
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
     * Constructs the map builder.
     *
     * @param RouteBuilderInterface $routeBuilder
     * @param null|RouteCollection $routes
     */
    public function __construct(
        RouteBuilderInterface $routeBuilder,
        ?RouteCollection $routes = null
    ) {
        $this->routeBuilder = $routeBuilder;
        $this->routes = $routes ?? new RouteCollection();
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
    public function addRoutes(RouteCollection $routes): MapBuilderInterface
    {
        $routes = $routes->toArray() + $this->routes->toArray();

        return new self($this->routeBuilder, new RouteCollection($routes));
    }

    /**
     * {@inheritdoc}
     */
    public function getMap($pathFinding = false): Map
    {
        if (false === $pathFinding) {
            $pathFinding = new NoPathFinding();
        } elseif (true === $pathFinding) {
            $pathFinding = new PathFinding($this->routeBuilder);
        } elseif (
            !\is_object($pathFinding) ||
            !$pathFinding instanceof PathFindingInterface
        ) {
            $message = \sprintf(
                'The argument must be of type boolean or %s, got an argument of type %s.',
                PathFindingInterface::class,
                \is_object($pathFinding) ?
                    \get_class($pathFinding) :
                    \gettype($pathFinding)
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        return new Map($pathFinding, $this->routes);
    }
}
