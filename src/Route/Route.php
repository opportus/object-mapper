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

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Point\CheckPointCollection;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;

/**
 * The route.
 *
 * @package Opportus\ObjectMapper\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class Route
{
    /**
     * @var string $fqn
     */
    private $fqn;

    /**
     * @var SourcePointInterface $sourcePoint
     */
    private $sourcePoint;

    /**
     * @var TargetPointInterface $targetPoint
     */
    private $targetPoint;

    /**
     * @var CheckPointCollection $checkPoints
     */
    private $checkPoints;

    /**
     * Constructs the route.
     *
     * @param SourcePointInterface $sourcePoint
     * @param TargetPointInterface $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function __construct(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ) {
        $this->sourcePoint = $sourcePoint;
        $this->targetPoint = $targetPoint;
        $this->checkPoints = $checkPoints;
        $this->fqn = \sprintf(
            '%s:%s',
            $sourcePoint->getFqn(),
            $targetPoint->getFqn()
        );
    }

    /**
     * Gets the Fully Qualified Name of the route.
     *
     * @return string
     */
    public function getFqn(): string
    {
        return $this->fqn;
    }

    /**
     * Get the source point of the route.
     *
     * @return SourcePointInterface
     */
    public function getSourcePoint(): SourcePointInterface
    {
        return $this->sourcePoint;
    }

    /**
     * Get the target point of the route.
     *
     * @return TargetPointInterface
     */
    public function getTargetPoint(): TargetPointInterface
    {
        return $this->targetPoint;
    }

    /**
     * Gets the checkpoints of the route.
     *
     * @return CheckPointCollection
     */
    public function getCheckPoints(): CheckPointCollection
    {
        return $this->checkPoints;
    }
}
