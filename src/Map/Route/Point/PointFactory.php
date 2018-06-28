<?php

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidPointException;
use Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidPropertyPointSyntaxException;
use Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidMethodPointSyntaxException;
use Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidParameterPointSyntaxException;

/**
 * The point factory.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactory implements PointFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSourcePoint(string $pointFqn) : SourcePointInterface
    {
        try {
            return new PropertyPoint($pointFqn);

        } catch (InvalidPropertyPointSyntaxException $propertyPointException) {}

        try {
            return new MethodPoint($pointFqn);

        } catch (InvalidMethodPointSyntaxException $methodPointException) {}

        throw new InvalidPointException(sprintf(
            '%s %s',
            $propertyPointException->getMessage(),
            $methodPointException->getMessage()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function createTargetPoint(string $pointFqn) : TargetPointInterface
    {
        try {
            return new PropertyPoint($pointFqn);

        } catch (InvalidPropertyPointSyntaxException $propertyPointException) {}

        try {
            return new ParameterPoint($pointFqn);

        } catch (InvalidParameterPointSyntaxException $parameterPointException) {}

        throw new InvalidPointException(sprintf(
            '%s %s',
            $propertyPointException->getMessage(),
            $parameterPointException->getMessage()
        ));
    }
}

