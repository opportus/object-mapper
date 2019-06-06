<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;

/**
 * The route.
 *
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class Route
{
    /**
     * @var string $fqn
     */
    private $fqn;

    /**
     * @var PropertyPoint|MethodPoint $sourcePoint
     */
    private $sourcePoint;

    /**
     * @var PropertyPoint|ParameterPoint $targetPoint
     */
    private $targetPoint;

    /**
     * Constructs the route.
     *
     * @param PropertyPoint|MethodPoint $sourcePoint
     * @param PropertyPoint|ParameterPoint $targetPoint
     * @throws InvalidArgumentException
     */
    public function __construct(object $sourcePoint, object $targetPoint)
    {
        if (!$sourcePoint instanceof PropertyPoint && !$sourcePoint instanceof MethodPoint) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "sourcePoint" passed to "%s" is invalid. Expects an argument of type "%s" or "%s", got an argument of type "%s".',
                __METHOD__,
                PropertyPoint::class,
                MethodPoint::class,
                \get_class($sourcePoint)
            ));
        }

        if (!$targetPoint instanceof PropertyPoint && !$targetPoint instanceof ParameterPoint) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "targetPoint" passed to "%s" is invalid. Expects an argument of type "%s" or "%s", got an argument of type "%s".',
                __METHOD__,
                PropertyPoint::class,
                ParameterPoint::class,
                \get_class($targetPoint)
            ));
        }

        $this->fqn = \sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn());

        $this->sourcePoint = $sourcePoint;
        $this->targetPoint = $targetPoint;
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
     * @return PropertyPoint|MethodPoint
     */
    public function getSourcePoint(): object
    {
        return $this->sourcePoint;
    }

    /**
     * Get the target point of the route.
     *
     * @return PropertyPoint|ParameterPoint
     */
    public function getTargetPoint(): object
    {
        return $this->targetPoint;
    }
}
