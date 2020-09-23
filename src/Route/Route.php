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
use Opportus\ObjectMapper\Point\ObjectPoint;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;

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
     * @var ObjectPoint $sourcePoint
     */
    private $sourcePoint;

    /**
     * @var ObjectPoint $targetPoint
     */
    private $targetPoint;

    /**
     * @var CheckPointCollection $checkPoints
     */
    private $checkPoints;

    /**
     * Constructs the route.
     *
     * @param ObjectPoint $sourcePoint
     * @param ObjectPoint $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function __construct(
        ObjectPoint $sourcePoint,
        ObjectPoint $targetPoint,
        CheckPointCollection $checkPoints
    ) {
        if (false === Source::hasPointType($sourcePoint)) {
            $message = \sprintf(
                '%s cannot be a source point.',
                \get_class($sourcePoint)
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        if (false === Target::hasPointType($targetPoint)) {
            $message = \sprintf(
                '%s cannot be a target point.',
                \get_class($sourcePoint)
            );

            throw new InvalidArgumentException(2, __METHOD__, $message);
        }

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
     * @return ObjectPoint
     */
    public function getSourcePoint(): ObjectPoint
    {
        return $this->sourcePoint;
    }

    /**
     * Get the target point of the route.
     *
     * @return ObjectPoint
     */
    public function getTargetPoint(): ObjectPoint
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
