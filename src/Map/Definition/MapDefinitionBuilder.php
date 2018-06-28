<?php

namespace Opportus\ObjectMapper\Map\Definition;

use Opportus\ObjectMapper\Map\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Exception\InvalidOperationException;

/**
 * The map definition builder.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Definition
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapDefinitionBuilder implements MapDefinitionBuilderInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\Route\RouteBuilderInterface $routeBuilder
     */
    protected $routeBuilder;

    /**
     * @var null|Opportus\ObjectMapper\Map\Definition\MapDefinitionPreparationInterface $mapDefinitionPreparation
     */
    protected $mapDefinitionPreparation;

    /**
     * Constructs the map definition builder.
     *
     * @param Opportus\ObjectMapper\Map\Route\RouteBuilderInterface $routeBuilder
     * @param null|Opportus\ObjectMapper\Map\Definition\MapDefinitionPreparationInterface $mapDefinitionPreparation
     */
    public function __construct(RouteBuilderInterface $routeBuilder, ?MapDefinitionPreparationInterface $mapDefinitionPreparation = null)
    {
        $this->routeBuilder             = $routeBuilder;
        $this->mapDefinitionPreparation = $mapDefinitionPreparation;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareMapDefinition(?MapDefinitionPreparationInterface $mapDefinitionPreparation = null) : MapDefinitionBuilderInterface
    {
        return new self($this->routeBuilder, $mapDefinitionPreparation ?? new MapDefinitionPreparation());
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id) : MapDefinitionBuilderInterface
    {
        if (null === $this->mapDefinitionPreparation) {
            throw new InvalidOperationException(sprintf(
                '%s must be called prior to call %s.',
                __CLASS__.'::prepareMapDefinition',
                __METHOD__
            ));
        }

        $mapDefinitionPreparation = clone $this->mapDefinitionPreparation;

        $mapDefinitionPreparation->setId($id);

        return new self($this->routeBuilder, $mapDefinitionPreparation);
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(string $sourcePointFqn, string $targetPointFqn) : MapDefinitionBuilderInterface
    {
        if (null === $this->mapDefinitionPreparation) {
            throw new InvalidOperationException(sprintf(
                '%s must be called prior to call %s.',
                __CLASS__.'::prepareMapDefinition',
                __METHOD__
            ));
        }

        $mapDefinitionPreparation = clone $this->mapDefinitionPreparation;

        $mapDefinitionPreparation->getRouteCollection()->addRoute($this->routeBuilder->buildRoute($sourcePointFqn, $targetPointFqn));

        return new self($this->routeBuilder, $mapDefinitionPreparation);
    }

    /**
     * {@inheritdoc}
     */
    public function buildMapDefinition() : MapDefinitionInterface
    {
        if (null === $this->mapDefinitionPreparation) {
            throw new InvalidOperationException(sprintf(
                '%s must be called prior to call %s.',
                __CLASS__.'::prepareMapDefinition',
                __METHOD__
            ));
        }

        return new MapDefinition(
            $this->mapDefinitionPreparation->getId() ?? $this->generateMapDefinitionId(),
            $this->mapDefinitionPreparation->getRouteCollection()
        );
    }

    /**
     * Generates an ID for the map definition.
     *
     * @return string
     */
    private function generateMapDefinitionId() : string
    {
        $random = random_bytes(16);

        assert(strlen($random) == 16);

        $random[6] = chr(ord($random[6]) & 0x0f | 0x40); // Sets version to 0100
        $random[8] = chr(ord($random[8]) & 0x3f | 0x80); // Sets bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($random), 4));
    }
}

