<?php

namespace Opportus\ObjectMapper\Map\Definition;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The map definition collection.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapDefinitionCollection implements MapDefinitionCollectionInterface
{
    /**
     * @var array $mapDefinitions
     */
    protected $mapDefinitions;

    /**
     * Constructs the map definition collection.
     *
     * @param array $mapDefinitions
     */
    public function __construct(array $mapDefinitions = array())
    {
        foreach ($mapDefinitions as $mapDefinition) {
            if (!$mapDefinition instanceof MapDefinitionInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Argument 1 passed to %s is invalid. Expects the argument to contain elements of type %s. Got an element of type %s.',
                    __METHOD__,
                    MapDefinitionInterface::class,
                    is_object($mapDefinition) ? get_class($mapDefinition) : gettype($mapDefinition)
                ));
            }
        }

        $this->mapDefinitions = $mapDefinitions;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapDefinitions() : array
    {
        return $this->mapDefinitions;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapDefinition(string $mapDefinitionId) : MapDefinitionInterface
    {
        return $this->mapDefinitions[$mapDefinitionId] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function addMapDefinition(MapDefinitionInterface $mapDefinition) : MapDefinitionCollectionInterface
    {
        $this->mapDefinitions[$mapDefinition->getId()] = $mapDefinition;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addMapDefinitions(array $mapDefinitions) : MapDefinitionCollectionInterface
    {
        foreach ($mapDefinitions as $mapDefinition) {
            if (!$mapDefinition instanceof MapDefinitionInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Argument 1 passed to %s is invalid. Expects the argument to contain elements of type %s. Got an element of type %s.',
                    __METHOD__,
                    MapDefinitionInterface::class,
                    is_object($mapDefinition) ? get_class($mapDefinition) : gettype($mapDefinition)
                ));
            }
        }

        foreach ($mapDefinitions as $mapDefinition) {
            $this->addMapDefinition($mapDefinition);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMapDefinition(string $mapDefinitionId) : MapDefinitionCollectionInterface
    {
        unset($this->mapDefinitions[$mapDefinitionId]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMapDefinitions() : MapDefinitionCollectionInterface
    {
        $this->mapDefinitions = array();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMapDefinition(string $mapDefinitionId) : bool
    {
        return isset($this->mapDefinitions[$mapDefinitionId]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasMapDefinitions() : bool
    {
        return !empty($this->mapDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->getMapDefinition($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->addMapDefinition($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->removeMapDefinition($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->hasMapDefinition($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->mapDefinitions);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->mapDefinitions);
    }

    /**
     * Deep clones the collection.
     */
    public function __clone()
    {
        $mapDefinitions = $this->getMapDefinitions();

        $this->removeMapDefinitions();

        foreach ($mapDefinitions as $mapDefinition) {
            $this->addMapDefinition(clone $mapDefinition);
        }
    }
}

