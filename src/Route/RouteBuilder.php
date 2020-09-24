<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Route;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Point\CheckPointCollection;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\PointFactoryInterface;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;

/**
 * The route builder.
 *
 * @package Opportus\ObjectMapper\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class RouteBuilder implements RouteBuilderInterface
{
    /**
     * @var PointFactoryInterface $pointFactory
     */
    private $pointFactory;

    /**
     * @var null|MapBuilderInterface $mapBuilder
     */
    private $mapBuilder;

    /**
     * @var null|SourcePointInterface $sourcePoint
     */
    private $sourcePoint;

    /**
     * @var null|TargetPointInterface $targetPoint
     */
    private $targetPoint;

    /**
     * @var CheckPointCollection $checkPoints
     */
    private $checkPoints;

    /**
     * Constructs the route builder.
     *
     * @param PointFactoryInterface $pointFactory
     * @param null|MapBuilderInterface $mapBuilder
     * @param null|SourcePointInterface $sourcePoint
     * @param null|TargetPointInterface $targetPoint
     * @param null|CheckPointCollection $checkPoints
     */
    public function __construct(
        PointFactoryInterface $pointFactory,
        ?MapBuilderInterface $mapBuilder = null,
        ?SourcePointInterface $sourcePoint = null,
        ?TargetPointInterface $targetPoint = null,
        ?CheckPointCollection $checkPoints = null
    ) {
        $this->pointFactory = $pointFactory;
        $this->mapBuilder = $mapBuilder;
        $this->sourcePoint = $sourcePoint;
        $this->targetPoint = $targetPoint;
        $this->checkPoints = $checkPoints ?? new CheckPointCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function setMapBuilder(
        MapBuilderInterface $mapBuilder
    ): RouteBuilderInterface {
        return new self(
            $this->pointFactory,
            $mapBuilder,
            $this->sourcePoint,
            $this->targetPoint,
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setStaticSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface {
        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $this->pointFactory->createStaticSourcePoint($sourcePointFqn),
            $this->targetPoint,
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setStaticTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface {
        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $this->sourcePoint,
            $this->pointFactory->createStaticTargetPoint($targetPointFqn),
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setDynamicSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface {
        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $this->pointFactory->createDynamicSourcePoint($sourcePointFqn),
            $this->targetPoint,
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setDynamicTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface {
        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $this->sourcePoint,
            $this->pointFactory->createDynamicTargetPoint($targetPointFqn),
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function addCheckPoint(
        CheckPointInterface $checkPoint,
        int $checkPointPosition = null
    ): RouteBuilderInterface {
        $checkPoints = $this->checkPoints->toArray();

        if (null === $checkPointPosition) {
            $checkPoints[] = $checkPoint;
        } else {
            $checkPoints[$checkPointPosition] = $checkPoint;
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $this->sourcePoint,
            $this->targetPoint,
            new CheckPointCollection($checkPoints)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getRoute(): Route
    {
        if (null === $this->sourcePoint || null === $this->targetPoint) {
            throw new InvalidOperationException(
                __METHOD__,
                'The source or target point of the route have not been set.'
            );
        }

        return new Route(
            $this->sourcePoint,
            $this->targetPoint,
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function addRouteToMapBuilder(): RouteBuilderInterface
    {
        if (null === $this->mapBuilder) {
            throw new InvalidOperationException(
                __METHOD__,
                'The map builder has not been set.'
            );
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder->addRoute($this->getRoute())
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getMapBuilder(): MapBuilderInterface
    {
        return $this->mapBuilder;
    }
}
