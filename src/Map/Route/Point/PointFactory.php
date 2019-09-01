<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\Exception\InvalidMethodPointSyntaxException;
use Opportus\ObjectMapper\Exception\InvalidParameterPointSyntaxException;
use Opportus\ObjectMapper\Exception\InvalidPointException;
use Opportus\ObjectMapper\Exception\InvalidPropertyPointSyntaxException;

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
        try {
            return new PropertyPoint($pointFqn);
        } catch (InvalidPropertyPointSyntaxException $propertyPointException) {
        }

        try {
            return new MethodPoint($pointFqn);
        } catch (InvalidMethodPointSyntaxException $methodPointException) {
        }

        try {
            return new ParameterPoint($pointFqn);
        } catch (InvalidParameterPointSyntaxException $parameterPointException) {
        }

        throw new InvalidPointException(\sprintf(
            '%s %s %s',
            $propertyPointException->getMessage(),
            $methodPointException->getMessage(),
            $parameterPointException->getMessage()
        ));
    }
}
