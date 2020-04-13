<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\AbstractImmutableCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The check point collection.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class CheckPointCollection extends AbstractImmutableCollection
{
    /**
     * Constructs the check point collection.
     *
     * @param CheckPointInterface[] $checkPoints
     * @throws InvalidArgumentException
     */
    public function __construct(array $checkPoints = [])
    {
        foreach ($checkPoints as $checkPoint) {
            if (
                !\is_object($checkPoint) ||
                !$checkPoint instanceof CheckPointInterface
            ) {
                throw new InvalidArgumentException(\sprintf(
                    'Argument "checkPoints" passed to "%s" is invalid. Expects the array to contain elements of type "%s". Got an element of type "%s".',
                    __METHOD__,
                    CheckPointInterface::class,
                    \is_object($checkPoint) ?
                        \get_class($checkPoint) : \gettype($checkPoint)
                ));
            }
        }

        parent::__construct($checkPoints);
    }
}
