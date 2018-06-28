<?php

namespace Opportus\ObjectMapper\Map\Definition;

use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;

/**
 * The map definition.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapDefinition implements MapDefinitionInterface
{
    /**
     * @var string $id
     */
    protected $id;

    /**
     * @var Opportus\ObjectMapper\Map\Route\RouteCollectionInterface $routeCollection
     */
    protected $routeCollection;

    /**
     * Constructs the map definition.
     *
     * @param string $id
     * @param Opportus\ObjectMapper\Map\Route\RouteCollectionInterface $routeCollection
     */
    public function __construct(string $id, RouteCollectionInterface $routeCollection)
    {
        $this->id = $id;
        $this->routeCollection = $routeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection() : RouteCollectionInterface
    {
        return clone $this->routeCollection;
    }
}

