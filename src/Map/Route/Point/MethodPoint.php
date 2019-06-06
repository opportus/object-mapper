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

use Opportus\ObjectMapper\Exception\InvalidMethodPointException;
use Opportus\ObjectMapper\Exception\InvalidMethodPointSyntaxException;
use ReflectionException;
use ReflectionMethod;

/**
 * The method point.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class MethodPoint
{
    use PointTrait;
    
    /**
     * Constructs the method point.
     *
     * @param  string $fqn
     * @throws InvalidMethodPointException
     * @throws InvalidMethodPointSyntaxException
     */
    public function __construct(string $fqn)
    {
        $regex = '/^([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/';

        if (!\preg_match($regex, $fqn, $matches)) {
            throw new InvalidMethodPointSyntaxException(\sprintf(
                '"%s" is not a method point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                $regex
            ));
        }

        list($matchedFqn, $matchedClassName, $matchedName) = $matches;

        try {
            $this->reflector = new ReflectionMethod($matchedClassName, $matchedName);
        } catch (ReflectionException $exception) {
            throw new InvalidMethodPointException(\sprintf(
                '"%s" is not a method point. %s.',
                $fqn,
                $exception->getMessage()
            ));
        }

        if ($this->getNumberOfRequiredParameters() > 0) {
            throw new InvalidMethodPointException(\sprintf(
                '"%s" is not a method point as such cannot have required parameters.',
                $fqn
            ));
        }

        $this->fqn = $fqn;

        $this->reflector->setAccessible(true);
    }

    /**
     * Gets the point value from the passed object.
     *
     * @param  null|object $object
     * @return mixed
     */
    public function getValue($object = null)
    {
        return $this->reflector->invoke($object);
    }
}
