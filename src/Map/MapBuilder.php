<?php

namespace Opportus\ObjectMapper\Map;

use Opportus\ObjectMapper\ClassCanonicalizerInterface;
use Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface;
use Opportus\ObjectMapper\Map\Definition\MapDefinitionInterface;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategy;
use Opportus\ObjectMapper\Map\Strategy\NoPathFindingStrategy;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;

/**
 * The map builder.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapBuilder implements MapBuilderInterface
{
    /**
     * @var Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     */
    private $classCanonicalizer;

    /**
     * @param Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface $mapDefinitionBuilder
     */
    private $mapDefinitionBuilder;

    /**
     * Constructs the map builder.
     *
     * @param Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     * @param Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface $mapDefinitionBuilder
     */
    public function __construct(ClassCanonicalizerInterface $classCanonicalizer, MapDefinitionBuilderInterface $mapDefinitionBuilder)
    {
        $this->classCanonicalizer = $classCanonicalizer;
        $this->mapDefinitionBuilder = $mapDefinitionBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareMap() : MapBuilderInterface
    {
        return new self($this->classCanonicalizer, $this->mapDefinitionBuilder->prepareMapDefinition());
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn) : MapBuilderInterface
    {
        try {
            return new self($this->classCanonicalizer, $this->mapDefinitionBuilder->addRoute($sourcePointFqn, $targetPointFqn));

        } catch (InvalidOperationException $exception) {
            throw new InvalidOperationException(sprintf(
                '%s must be called prior to call %s.',
                __CLASS__.'::prepareMap',
                __METHOD__
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildMap($parameter = null) : MapInterface
    {
        if ((!null === $parameter) &&
            (!$parameter instanceof PathFindingStrategyInterface) &&
            (!$parameter instanceof MapDefinitionInterface)
        ) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s is invalid. Expects the argument to be of type null or %s or %s. Got an argument of type %s.',
                __METHOD__,
                PathFindingStrategyInterface::class,
                MapDefinitionInterface::class,
                is_object($parameter) ? get_class($parameter) : gettype($parameter)
            ));
        }

        try {
            $pathFindingStrategy = new NoPathFindingStrategy($this->classCanonicalizer, $this->mapDefinitionBuilder->buildMapDefinition());

            if (null !== $parameter) {
                throw new InvalidArgumentException(sprintf(
                    'Argument 1 passed to %s is invalid. Expects the argument to be of type null when the map builder has a map in preparation.',
                    __METHOD__
                ));
            }

        } catch (InvalidOperationException $exception) {
            if (null === $parameter) {
                $pathFindingStrategy = new PathFindingStrategy($this->classCanonicalizer, $this->mapDefinitionBuilder);

            } elseif ($parameter instanceof MapDefinitionInterface) {
                $pathFindingStrategy = new NoPathFindingStrategy($parameter);

            } elseif ($parameter instanceof PathFindingStrategyInterface) {
                $pathFindingStrategy = $parameter;
            }
        }

        return new Map($pathFindingStrategy);
    }
}

