<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
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
class RouteBuilder implements RouteBuilderInterface
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
        try {
            $sourcePoint = $this->pointFactory
                ->createStaticSourcePoint($sourcePointFqn);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(1, '', 0, $exception);
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $sourcePoint,
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
        try {
            $targetPoint = $this->pointFactory
                ->createStaticTargetPoint($targetPointFqn);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(1, '', 0, $exception);
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $this->sourcePoint,
            $targetPoint,
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setDynamicSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface {
        try {
            $sourcePoint = $this->pointFactory
                ->createDynamicSourcePoint($sourcePointFqn);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(1, '', 0, $exception);
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $sourcePoint,
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
        try {
            $targetPoint = $this->pointFactory
                ->createDynamicTargetPoint($targetPointFqn);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(1, '', 0, $exception);
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $this->sourcePoint,
            $targetPoint,
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setSourcePoint(
        string $sourcePointFqn
    ): RouteBuilderInterface {
        try {
            $sourcePoint = $this->pointFactory
                ->createSourcePoint($sourcePointFqn);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(1, '', 0, $exception);
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $sourcePoint,
            $this->targetPoint,
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setTargetPoint(
        string $targetPointFqn
    ): RouteBuilderInterface {
        try {
            $targetPoint = $this->pointFactory
                ->createTargetPoint($targetPointFqn);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(1, '', 0, $exception);
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder,
            $this->sourcePoint,
            $targetPoint,
            $this->checkPoints
        );
    }

    /**
     * {@inheritDoc}
     */
    public function addCheckPoint(
        CheckPointInterface $checkPoint,
        ?int $checkPointPosition = null
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
     * {@inheritdoc}
     */
    public function addRecursionCheckPoint(
        string $sourceFqn,
        string $targetFqn,
        string $targetSourcePointFqn,
        ?int $checkPointPosition = null
    ): RouteBuilderInterface {
        try {
            $checkPoint = $this->pointFactory->createRecursionCheckPoint(
                $sourceFqn,
                $targetFqn,
                $targetSourcePointFqn
            );
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                $exception->getArgument(),
                '',
                0,
                $exception
            );
        }

        return $this->addCheckPoint(
            $checkPoint,
            $checkPointPosition
        );
    }

    /**
     * {@inheritdoc}
     */
    public function addIterableRecursionCheckPoint(
        string $sourceFqn,
        string $targetFqn,
        string $targetIterableSourcePointFqn,
        ?int $checkPointPosition = null
    ): RouteBuilderInterface {
        try {
            $checkPoint = $this->pointFactory->createIterableRecursionCheckPoint(
                $sourceFqn,
                $targetFqn,
                $targetIterableSourcePointFqn
            );
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                $exception->getArgument(),
                '',
                0,
                $exception
            );
        }

        return $this->addCheckPoint(
            $checkPoint,
            $checkPointPosition
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getRoute(): RouteInterface
    {
        if (null === $this->sourcePoint || null === $this->targetPoint) {
            throw new InvalidOperationException(
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
                'The map builder has not been set.'
            );
        }

        try {
            $route = $this->getRoute();
        } catch (InvalidOperationException $exception) {
            throw new InvalidOperationException('', 0, $exception);
        }

        return new self(
            $this->pointFactory,
            $this->mapBuilder->addRoute($route)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getMapBuilder(): ?MapBuilderInterface
    {
        return $this->mapBuilder;
    }
}
