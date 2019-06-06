<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @var RouteCollection $routes
     */
    private $routes;

    /**
     * Constructs the no path finding strategy.
     *
     * @param RouteCollection $routes
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
