<?php

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;;

/**
 * The no path finding strategy.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class NoPathFindingStrategy implements PathFindingStrategyInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface $mapDefinition
     */
    protected $mapDefinition;

    /**
     * Constructs the no path finding strategy.
     *
     * @param Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface $mapDefinition
     */
    public function __construct(MapDefinitionInterface $mapDefinition)
    {
        $this->mapDefinition = $mapDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(string $sourceClassFqn, string $targetClassFqn) : RouteCollectionInterface
    {
        if (!class_exists($sourceClassFqn)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s is invalid. Class %s does not exist.',
                __METHOD__,
                $sourceClassFqn
            ));
        }

        if (!class_exists($targetClassFqn)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s is invalid. Class %s does not exist.',
                __METHOD__,
                $targetClassFqn
            ));
        }

        $routeCollection = new RouteCollection();

        foreach ($this->mapDefinition->getRouteCollection() as $route) {
            if ($sourceClassFqn === $route->getSourcePoint()->getClassFqn() && $targetClassFqn === $route->getTargetPoint()->getClassFqn()) {
                $routeCollection->addRoute($route);
            }
        }

        return $routeCollection;
    }
}

