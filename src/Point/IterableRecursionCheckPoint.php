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

use ArrayAccess;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\ObjectMapperTrait;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\TargetInterface;
use Traversable;

/**
 * The iterable recursion check point.
 *
 * @package Opportus\ObjectMapper\Point
 * @author Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class IterableRecursionCheckPoint implements CheckPointInterface
{
    use ObjectMapperTrait;

    /**
     * @var string $sourceFqn
     */
    private $sourceFqn;

    /**
     * @var string $targetFqn
     */
    private $targetFqn;

    /**
     * @var SourcePointInterface $targetIterableSourcePoint
     */
    private $targetIterableSourcePoint;

    /**
     * Constructs the iterable recursion check point.
     *
     * @param string $sourceFqn
     * @param string $targetFqn
     * @param SourcePointInterface $targetIterableSourcePoint
     */
    public function __construct(
        string $sourceFqn,
        string $targetFqn,
        SourcePointInterface $targetIterableSourcePoint
    ) {
        $this->sourceFqn = $sourceFqn;
        $this->targetFqn = $targetFqn;
        $this->targetIterableSourcePoint = $targetIterableSourcePoint;
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
            return null;
        }

        $recursionSourceIterable = $subject;

        if (false === $this->isIterable($recursionSourceIterable)) {
            $message = \sprintf(
                'Recursion source iterable of type %s on route %s is not iterable.',
                \is_object($recursionSourceIterable) ?
                    \get_class($recursionSourceIterable) :
                    \gettype($recursionSourceIterable),
                $route->getFqn()
            );

            throw new InvalidOperationException(__METHOD__, $message);
        }

        if (false === $target->isInstantiated()) {
            $recursionTargetIterable = [];
        } else {
            $recursionTargetIterable = (new Source($target->getInstance()))
                ->getPointValue($this->targetIterableSourcePoint);
        }

        if (false === $this->isIterable($recursionTargetIterable)) {
            $message = \sprintf(
                'Recursion target iterable of type %s on route %s is not iterable.',
                \is_object($recursionTargetIterable) ?
                    \get_class($recursionTargetIterable) :
                    \gettype($recursionTargetIterable),
                $route->getFqn()
            );

            throw new InvalidOperationException(__METHOD__, $message);
        }

        foreach ($recursionSourceIterable as $i => $recursionSource) {
            $recursionSource = new Source($recursionSource);

            if ($recursionSource->getFqn() !== $this->sourceFqn) {
                $message = \sprintf(
                    'Iterable recursion source %s does not match iterable recursion source %s being mapped on route %s.',
                    $this->sourceFqn,
                    $recursionSource->getFqn(),
                    $route->getFqn()
                );

                throw new InvalidOperationException(__METHOD__, $message);
            }

            if (!isset($recursionTargetIterable[$i])) {
                $recursionTarget = new Target($this->targetFqn);
            } else {
                $recursionTarget = new Target($recursionTargetIterable[$i]);

                if ($recursionTarget->getFqn() !== $this->targetFqn) {
                    $message = \sprintf(
                        'Iterable recursion target %s does not match iterable recursion target %s being mapped on route %s.',
                        $this->targetFqn,
                        $recursionTarget->getFqn(),
                        $route->getFqn()
                    );

                    throw new InvalidOperationException(__METHOD__, $message);
                }
            }

            $updatedRecursionTarget = $this->mapObjects(
                $recursionSource,
                $recursionTarget,
                $map
            );

            if (null === $updatedRecursionTarget) {
                $message = \sprintf(
                    'No route found for iterable recursion source %s and iterable recursion target %s on route %s.',
                    $recursionSource->getFqn(),
                    $recursionTarget->getFqn(),
                    $route->getFqn()
                );

                throw new InvalidOperationException(__METHOD__, $message);
            }

            $recursionTargetIterable[$i] = $updatedRecursionTarget;
        }

        return $recursionTargetIterable;
    }

    /**
     * Checks whether the passed argument is iterable.
     *
     * @param $arg
     * @return bool
     */
    private function isIterable($arg): bool
    {
        return
            \is_array($arg) ||
            (
                $arg instanceof Traversable &&
                $arg instanceof ArrayAccess
            );
    }
}
