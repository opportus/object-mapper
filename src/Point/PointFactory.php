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

use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The point factory.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class PointFactory implements PointFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createObjectPoint(string $objectPointFqn): ObjectPoint
    {
        if (\preg_match(
            MethodObjectPoint::FQN_SYNTAX_PATTERN,
            $objectPointFqn
        )) {
            return new MethodObjectPoint($objectPointFqn);
        } elseif (\preg_match(
            ParameterObjectPoint::FQN_SYNTAX_PATTERN,
            $objectPointFqn
        )) {
            return new ParameterObjectPoint($objectPointFqn);
        } elseif (\preg_match(
            PropertyObjectPoint::FQN_SYNTAX_PATTERN,
            $objectPointFqn
        )) {
            return new PropertyObjectPoint($objectPointFqn);
        }

        $message = \sprintf('%s is not a point FQN.', $objectPointFqn);

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }
}
