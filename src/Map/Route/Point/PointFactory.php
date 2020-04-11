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

use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The point factory.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class PointFactory implements PointFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createPoint(string $pointFqn): object
    {
        if (\preg_match(MethodPoint::SYNTAX_PATTERN, $pointFqn)) {
            return new MethodPoint($pointFqn);
        } elseif (\preg_match(ParameterPoint::SYNTAX_PATTERN, $pointFqn)) {
            return new ParameterPoint($pointFqn);
        } elseif (\preg_match(PropertyPoint::SYNTAX_PATTERN, $pointFqn)) {
            return new PropertyPoint($pointFqn);
        }

        throw new InvalidArgumentException(\sprintf(
            'Argument "pointFqn" passed to "%s" is invalid. Expecting the argument to match a valid point FQN syntax pattern. Got "%s".',
            __METHOD__,
            $pointFqn
        ));
    }
}
