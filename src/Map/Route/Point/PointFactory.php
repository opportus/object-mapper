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
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class PointFactory implements PointFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createPoint(string $pointFqn): AbstractPoint
    {
        if (\preg_match(MethodPoint::FQN_SYNTAX_PATTERN, $pointFqn)) {
            return new MethodPoint($pointFqn);
        } elseif (\preg_match(ParameterPoint::FQN_SYNTAX_PATTERN, $pointFqn)) {
            return new ParameterPoint($pointFqn);
        } elseif (\preg_match(PropertyPoint::FQN_SYNTAX_PATTERN, $pointFqn)) {
            return new PropertyPoint($pointFqn);
        }

        throw new InvalidArgumentException(\sprintf(
            'Argument "pointFqn" passed to "%s" is invalid. "%s" is not a valid point FQN.',
            __METHOD__,
            $pointFqn
        ));
    }
}
