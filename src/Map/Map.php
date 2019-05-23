<?php

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;

/**
 * The map.
 *
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class Map implements MapInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface $pathFindingStrategy
     */
    protected $pathFindingStrategy;

    /**
     * Constructs the map.
     *
     * @param Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface $pathFindingStrategy
     */
    public function __construct(PathFindingStrategyInterface $pathFindingStrategy)
    {
        $this->pathFindingStrategy = $pathFindingStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(object $source, $target): RouteCollectionInterface
    {
        return $this->pathFindingStrategy->getRouteCollection($source, $target);
    }

    /**
     * {@inheritdoc}
     */
    public function getPathFindingStrategyType(): string
    {
        return get_class($this->pathFindingStrategy);
    }
}
