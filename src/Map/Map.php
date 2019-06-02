<?php

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Filter\FilterCollection;
use Opportus\ObjectMapper\Map\Filter\FilterInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Route\Route;
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
     * @var Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface $pathFindingStrategy
     */
    private $pathFindingStrategy;

    /**
     * @var Opportus\ObjectMapper\Map\Filter\FilterCollection $filters
     */
    private $filters;

    /**
     * Constructs the map.
     *
     * @param Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface $pathFindingStrategy
     * @param null|Opportus\ObjectMapper\Map\Filter\FilterCollection $filters
     */
    public function __construct(PathFindingStrategyInterface $pathFindingStrategy, ?FilterCollection $filters = null)
    {
        $this->pathFindingStrategy = $pathFindingStrategy;
        $this->filters = $filters ?? new FilterCollection();
    }

    /**
     * Checks whether the map has any route connecting the points of the passed source with the points of the passed target.
     * 
     * @param Opportus\ObjectMapper\Context $context
     * @return bool
     */
    public function hasRoutes(Context $context): bool
    {
        return (bool) \count($this->getRoutes($context));
    }

    /**
     * Gets the routes connecting the points of the source with the points of the target.
     *
     * @param Opportus\ObjectMapper\Context $context
     * @return Opportus\ObjectMapper\Map\Route\RouteCollection
     */
    public function getRoutes(Context $context): RouteCollection
    {
        return $this->pathFindingStrategy->getRoutes($context);
    }

    /**
     * Gets the filter on the passed route.
     * 
     * @param Opportus\ObjectMapper\Map\Route\Route $route
     * @return null|Opportus\ObjectMapper\Map\Filter\FilterInterface
     */
    public function getFilterOnRoute(Route $route): ?FilterInterface
    {
        return $this->filters[$route->getFqn()] ?? null;
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
