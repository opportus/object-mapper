<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\ImmutableCollection;

/**
 * The check point collection.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class CheckPointCollection extends ImmutableCollection
{
    /**
     * Constructs the check point collection.
     *
     * @param CheckPointInterface[] $checkPoints
     * @throws InvalidArgumentException
     */
    public function __construct(array $checkPoints = [])
    {
        foreach ($checkPoints as $index=> $checkPoint) {
            if (
                !\is_object($checkPoint) ||
                !$checkPoint instanceof CheckPointInterface
            ) {
                $message = \sprintf(
                    'The array must contain exclusively elements of type %s, got an element of type %s.',
                    CheckPointInterface::class,
                    \is_object($checkPoint) ?
                        \get_class($checkPoint) : \gettype($checkPoint)
                );

                throw new InvalidArgumentException(1, $message);
            }

            if (false === \is_int($index)) {
                $message = \sprintf(
                    'The array must contain exclusively indexes of type integer, got an index of type %s.',
                    \gettype($index)
                );

                throw new InvalidArgumentException(1, $message);
            }
        }

        \ksort($checkPoints, \SORT_NUMERIC);

        parent::__construct($checkPoints);
    }
}
