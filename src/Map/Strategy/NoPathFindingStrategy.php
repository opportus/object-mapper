<?php

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\RouteCollection;

/**
 * The no path finding strategy.
 *
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class NoPathFindingStrategy implements PathFindingStrategyInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\Route\RouteCollection $routes
     */
    private $routes;

    /**
     * Constructs the no path finding strategy.
     *
     * @param Opportus\ObjectMapper\Map\Route\RouteCollection $routes
     */
    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes(Context $context): RouteCollection
    {
        $routes = [];

        foreach ($this->routes as $route) {
            if ($context->getSourceClassFqn() === $route->getSourcePoint()->getClassFqn() && $context->getTargetClassFqn() === $route->getTargetPoint()->getClassFqn()) {
                $routes[] = $route;
            }
        }

        return new RouteCollection($routes);
    }
}
