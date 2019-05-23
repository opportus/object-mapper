<?php

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\ClassCanonicalizerInterface;
use Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;;

/**
 * The no path finding strategy.
 *
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class NoPathFindingStrategy implements PathFindingStrategyInterface
{
    /**
     * @var Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     */
    private $classCanonicalizer;

    /**
     * @var Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface $mapDefinition
     */
    private $mapDefinition;

    /**
     * Constructs the no path finding strategy.
     *
     * @param Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     * @param Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface $mapDefinition
     */
    public function __construct(ClassCanonicalizerInterface $classCanonicalizer, MapDefinitionInterface $mapDefinition)
    {
        $this->classCanonicalizer = $classCanonicalizer;
        $this->mapDefinition = $mapDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(object $source, $target): RouteCollectionInterface
    {
        $sourceFqcn = $this->classCanonicalizer->getCanonicalFqcn($source);
        $targetFqcn = $this->classCanonicalizer->getCanonicalFqcn($target);

        $routeCollection = new RouteCollection();

        foreach ($this->mapDefinition->getRouteCollection() as $route) {
            if ($sourceFqcn === $route->getSourcePoint()->getClassFqn() && $targetFqcn === $route->getTargetPoint()->getClassFqn()) {
                $routeCollection->addRoute($route);
            }
        }

        return $routeCollection;
    }
}
