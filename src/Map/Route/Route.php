<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Point\AbstractPoint;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;

/**
 * The route.
 *
 * @package Opportus\ObjectMapper\Map\Route
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
     * @var AbstractPoint $sourcePoint
     */
    private $sourcePoint;

    /**
     * @var AbstractPoint $targetPoint
     */
    private $targetPoint;

    /**
     * @var CheckPointCollection $checkPoints
     */
    private $checkPoints;

    /**
     * Constructs the route.
     *
     * @param AbstractPoint $sourcePoint
     * @param AbstractPoint $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function __construct(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        CheckPointCollection $checkPoints
    ) {
        if (false === Source::isValidPoint($sourcePoint)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "sourcePoint" passed to "%s" is invalid. "%s" cannot be a source point.',
                __METHOD__,
                \get_class($sourcePoint)
            ));
        }

        if (false === Target::isValidPoint($targetPoint)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "targetPoint" passed to "%s" is invalid. "%s" cannot be a target point.',
                __METHOD__,
                \get_class($targetPoint)
            ));
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
     * @return AbstractPoint
     */
    public function getSourcePoint(): AbstractPoint
    {
        return $this->sourcePoint;
    }

    /**
     * Get the target point of the route.
     *
     * @return AbstractPoint
     */
    public function getTargetPoint(): AbstractPoint
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
