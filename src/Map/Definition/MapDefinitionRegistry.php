<?php

namespace Opportus\ObjectMapper\Map\Definition;

use Opportus\ObjectMapper\Exception\InvalidOperationException;

/**
 * The map definition registry.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapDefinitionRegistry implements MapDefinitionRegistryInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\Definition\MapDefinitionCollectionInterface $mapDefinitionCollection
     */
    protected $mapDefinitionCollection;

    /**
     * Constructs the map definition registry.
     *
     * @param null|Opportus\ObjectMapper\Map\Definition\MapDefinitionCollectionInterface $mapDefinitionCollection
     */
    public function __construct(?MapDefinitionCollectionInterface $mapDefinitionCollection = null)
    {
        $this->mapDefinitionCollection = $mapDefinitionCollection ?? new MapDefinitionCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getMapDefinition(string $mapDefinitionId) : ?MapDefinitionInterface
    {
        return $this->mapDefinitionCollection->getMapDefinition($mapDefinitionId);
    }

    /**
     * {@inheritdoc}
     */
    public function addMapDefinition(MapDefinitionInterface $mapDefinition) : MapDefinitionRegistryInterface
    {
        if ($this->mapDefinitionCollection->hasMapDefinition($mapDefinition->getId())) {
            if (spl_object_hash($mapDefinition) !== spl_object_hash($this->getMapDefinition($mapDefinition->getId()))) {
                throw new InvalidOperationException(sprintf(
                    'Cannot register the map definition. The registry contains already a different map definition having the same ID "%s".',
                    $mapDefinition->getId()
                ));
            }
        }

        $this->mapDefinitionCollection->addMapDefinition($mapDefinition);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMapDefinition(string $mapDefinitionId) : MapDefinitionRegistryInterface
    {
        $this->mapDefinitionCollection->removeMapDefinition($mapDefinitionId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMapDefinition(string $mapDefinitionId) : bool
    {
        return $this->mapDefinitionCollection->hasMapDefinition($mapDefinitionId);
    }
}

