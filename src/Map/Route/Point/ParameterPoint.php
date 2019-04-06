<?php

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidParameterPointException;
use Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidParameterPointSyntaxException;

/**
 * The parameter point.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ParameterPoint extends Point implements TargetPointInterface
{
    /**
     * Constructs the parameter point.
     *
     * @param  string $fqn
     * @throws Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidParameterPointException
     * @throws Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidParameterPointSyntaxException
     */
    public function __construct(string $fqn)
    {
        $regex = '/([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)/';

        if (!preg_match($regex, $fqn, $matches)) {
            throw new InvalidParameterPointSyntaxException(sprintf(
                '"%s" is not a parameter point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                $regex
            ));
        }

        list($matchedFqn, $matchedClassName, $matchedMethodName, $matchedName) = $matches;

        try {
            $this->reflector = new \ReflectionParameter(array($matchedClassName, $matchedMethodName), $matchedName);

        } catch (\ReflectionException $reflectionException) {
            throw new InvalidParameterPointException(sprintf(
                '"%s" is not a parameter point. %s.',
                $fqn,
                $reflectionException->getMessage()
            ));
        }

        $this->fqn = $fqn;

        $this->reflector->getDeclaringFunction()->setAccessible(true);
    }

    /**
     * Gets the name of the method of the point.
     *
     * @return string
     */
    public function getMethodName() : string
    {
        return $this->reflector->getDeclaringFunction()->getName();
    }
}

