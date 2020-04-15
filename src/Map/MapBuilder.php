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
use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Map\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Strategy\NoPathFindingStrategy;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategy;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;

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
    public function addRoute(
        string $sourcePointFqn,
        string $targetPointFqn,
        ?CheckPointCollection $checkPoints = null
    ): MapBuilderInterface {
        $routes = $this->routes->toArray();

        $routes[] = $this->routeBuilder->buildRoute(
            $sourcePointFqn,
            $targetPointFqn,
            $checkPoints ?? new CheckPointCollection()
        );

        return new self($this->routeBuilder, new RouteCollection($routes));
    }

    /**
     * {@inheritdoc}
     */
    public function buildMap($pathFindingStrategy = false): Map
    {
        if (false === $pathFindingStrategy) {
            $pathFindingStrategy = new NoPathFindingStrategy();
        } elseif (true === $pathFindingStrategy) {
            $pathFindingStrategy = new PathFindingStrategy($this->routeBuilder);
        } elseif (
            !\is_object($pathFindingStrategy) ||
            !$pathFindingStrategy instanceof PathFindingStrategyInterface
        ) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "pathFindingStrategy" passed to "%s" is invalid. Expects an argument of type "%s" or "%s", got an argument of type "%s".',
                __METHOD__,
                'boolean',
                PathFindingStrategyInterface::class,
                \is_object($pathFindingStrategy) ?
                    \get_class($pathFindingStrategy) :
                    \gettype($pathFindingStrategy)
            ));
        }

        return new Map($pathFindingStrategy, $this->routes);
    }
}
