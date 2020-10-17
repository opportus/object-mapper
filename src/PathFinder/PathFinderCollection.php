<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
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
        foreach ($pathFinders as $pathFinder) {
            if (!\is_object($pathFinder) || !$pathFinder instanceof PathFinderInterface) {
                $message = \sprintf(
                    'The array must contain exclusively elements of type %s, got an element of type %s.',
                    PathFinderInterface::class,
                    \is_object($pathFinder) ? \get_class($pathFinder) : \gettype($pathFinder)
                );

                throw new InvalidArgumentException(1, __METHOD__, $message);
            }
        }

        parent::__construct($pathFinders);
    }
}
