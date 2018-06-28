<?php

namespace Opportus\ObjectMapper\Map\Definition;

use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;

/**
 * The map definition preparation.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 * @internal
 */
class MapDefinitionPreparation implements MapDefinitionPreparationInterface
{
    /**
     * @var null|string $id
     */
    protected $id;

    /**
     * @var Opportus\ObjectMapper\Map\Route\RouteCollectionInterface $routeCollection
     */
    protected $routeCollection;

    /**
     * Constructs the map definition preparation.
     *
     * @param null|string $id
     * @param null|Opportus\ObjectMapper\Map\Route\RouteCollectionInterface $routeCollection
     */
    public function __construct(?string $id = null, ?RouteCollectionInterface $routeCollection = null)
    {
        $this->id = $id;
        $this->routeCollection = $routeCollection ?? new RouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id) : MapDefinitionPreparationInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId() : ?string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouteCollection(RouteCollectionInterface $routeCollection) : MapDefinitionPreparationInterface
    {
        $this->routeCollection = $routeCollection;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection() : RouteCollectionInterface
    {
        return $this->routeCollection;
    }

    /**
     * Deep clones the map definition preparation.
     */
    public function __clone()
    {
        $this->routeCollection = clone $this->routeCollection;
    }
}

