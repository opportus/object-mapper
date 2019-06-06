<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Filter\Filter;
use Opportus\ObjectMapper\Map\Filter\FilterCollection;
use Opportus\ObjectMapper\Map\Filter\FilterInterface;
use Opportus\ObjectMapper\Map\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Strategy\NoPathFindingStrategy;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategy;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;

/**
 * The map builder.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <opportus@gmail.com>
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
     * @var FilterCollection $filters
     */
    private $filters;

    /**
     * Constructs the map builder.
     *
     * @param RouteBuilderInterface $routeBuilder
     * @param null|RouteCollection $routes
     * @param null|FilterCollection $filters
     */
    public function __construct(RouteBuilderInterface $routeBuilder, ?RouteCollection $routes = null, ?FilterCollection $filters = null)
    {
        $this->routeBuilder = $routeBuilder;
        $this->routes = $routes ?? new RouteCollection();
        $this->filters = $filters ?? new FilterCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn, $filter = null): MapBuilderInterface
    {
        $route = $this->routeBuilder->buildRoute($sourcePointFqn, $targetPointFqn);

        $routes = $this->routes->toArray();
        $filters = $this->filters->toArray();

        $routes[] = $route;

        if (null !== $filter) {
            if (\is_callable($filter)) {
                $filter = new Filter($route, $filter);

            } elseif (!\is_object($filter) || !$filter instanceof FilterInterface) {
                throw new InvalidArgumentException(\sprintf(
                    'Argument "filter" passed to "%s" is invalid. Expects an argument of type "%s" or "%s", got an argument of type "%s".',
                    __METHOD__,
                    'Callable',
                    FilterInterface::class,
                    \is_object($filter) ? \get_class($filter) : \gettype($filter)
                ));

            } elseif ($filter->getRouteFqn() !== $route->getFqn()) {
                throw new InvalidArgumentException(\sprintf(
                    'Argument "filter" passed to "%s" is invalid. Filter route FQN "%s" does not match the added route FQN "%s".',
                    __METHOD__,
                    $filter->getRouteFqn(),
                    $route->getFqn()
                ));
            }

            $filters[] = $filter;
        }

        return new self($this->routeBuilder, new RouteCollection($routes), new FilterCollection($filters));
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(FilterInterface $filter): MapBuilderInterface
    {
        $filters = $this->filters->toArray();

        $filters[] = $filter;
        
        return new self($this->routeBuilder, $this->routes, new FilterCollection($filters));
    }

    /**
     * {@inheritdoc}
     */
    public function buildMap($pathFindingStrategy = false): Map
    {
        if (false === $pathFindingStrategy) {
            $pathFindingStrategy = new NoPathFindingStrategy($this->routes);

        } elseif (true === $pathFindingStrategy) {
            $pathFindingStrategy = new PathFindingStrategy();

        } elseif (!\is_object($pathFindingStrategy) || !$pathFindingStrategy instanceof PathFindingStrategyInterface) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "pathFindingStrategy" passed to "%s" is invalid. Expects an argument of type "%s" or "%s", got an argument of type "%s".',
                __METHOD__,
                'boolean',
                PathFindingStrategyInterface::class,
                \is_object($pathFindingStrategy) ? \get_class($pathFindingStrategy) : \gettype($pathFindingStrategy)
            ));
        }

        return new Map($pathFindingStrategy, $this->filters);
    }
}
