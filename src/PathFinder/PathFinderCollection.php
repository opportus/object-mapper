<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinder;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\ImmutableCollection;

/**
 * The path finder collection.
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PathFinderCollection extends ImmutableCollection
{
    /**
     * Constructs the path finder collection.
     *
     * @param PathFinderInterface[] $pathFinders
     * @throws InvalidArgumentException
     */
    public function __construct(array $pathFinders = [])
    {
        foreach ($pathFinders as $index => $pathFinder) {
            if (!\is_object($pathFinder) || !$pathFinder instanceof PathFinderInterface) {
                $message = \sprintf(
                    'The array must contain exclusively elements of type %s, got an element of type %s.',
                    PathFinderInterface::class,
                    \is_object($pathFinder) ? \get_class($pathFinder) : \gettype($pathFinder)
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

        \ksort($pathFinders, \SORT_NUMERIC);

        parent::__construct($pathFinders);
    }
}
