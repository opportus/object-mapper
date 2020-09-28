<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\ObjectMapperInterface;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\TargetInterface;

/**
 * The recursion check point.
 *
 * @package Opportus\ObjectMapper\Point
 * @author Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class RecursionCheckPoint implements CheckPointInterface
{
    /**
     * @var string $sourceFqn
     */
    private $sourceFqn;

    /**
     * @var string $targetFqn
     */
    private $targetFqn;

    /**
     * @var SourcePointInterface $targetSourcePoint
     */
    private $targetSourcePoint;

    /**
     * @var ObjectMapperInterface $objectMapper
     */
    private $objectMapper;

    /**
     * Constructs the recursion check point.
     *
     * @param string $sourceFqn
     * @param string $targetFqn
     * @param SourcePointInterface $targetSourcePoint
     * @param ObjectMapperInterface $objectMapper
     */
    public function __construct(
        string $sourceFqn,
        string $targetFqn,
        SourcePointInterface $targetSourcePoint,
        ObjectMapperInterface $objectMapper
    ) {
        $this->sourceFqn = $sourceFqn;
        $this->targetFqn = $targetFqn;
        $this->targetSourcePoint = $targetSourcePoint;
        $this->objectMapper = $objectMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function control(
        $subject,
        RouteInterface $route,
        MapInterface $map,
        SourceInterface $source,
        TargetInterface $target
    ) {
        if (null === $subject) {
            return $subject;
        }

        $recursionSource = new Source($subject);

        if ($recursionSource->getFqn() !== $this->sourceFqn) {
            $message = \sprintf(
                'Recursion source %s does not match recursion source %s being mapped on route %s.',
                $this->sourceFqn,
                $recursionSource->getFqn(),
                $route->getFqn()
            );

            throw new InvalidOperationException(__METHOD__, $message);
        }

        if (false === $target->isInstantiated()) {
            $recursionTarget = new Target($this->targetFqn);
        } else {
            $recursionTarget = (new Source($target->getInstance()))
                ->getPointValue($this->targetSourcePoint);

            if (null === $recursionTarget) {
                $recursionTarget = new Target($this->targetFqn);
            } else {
                $recursionTarget = new Target($recursionTarget);

                if ($recursionTarget->getFqn() !== $this->targetFqn) {
                    $message = \sprintf(
                        'Recursion target %s does not match recursion target %s being mapped on route %s.',
                        $this->targetFqn,
                        $recursionTarget->getFqn(),
                        $route->getFqn()
                    );

                    throw new InvalidOperationException(__METHOD__, $message);
                }
            }
        }

        $updatedRecursionTarget = $this->objectMapper->map(
            $recursionSource,
            $recursionTarget,
            $map
        );

        if (null === $updatedRecursionTarget) {
            $message = \sprintf(
                'No route found for recursion source %s and recursion target %s on route %s.',
                $recursionSource->getFqn(),
                $recursionTarget->getFqn(),
                $route->getFqn()
            );

            throw new InvalidOperationException(__METHOD__, $message);
        }

        return $updatedRecursionTarget;
    }
}
