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

use Opportus\ObjectMapper\Exception\InvalidParameterPointException;
use Opportus\ObjectMapper\Exception\InvalidParameterPointSyntaxException;
use ReflectionException;
use ReflectionParameter;

/**
 * The parameter point.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class ParameterPoint
{
    use PointTrait;

    /**
     * @var ReflectionParameter $reflector
     */
    private $reflector;

    /**
     * @var string $methodName
     */
    private $methodName;

    /**
     * @var int $position
     */
    private $position;

    /**
     * Constructs the parameter point.
     *
     * @param string $fqn
     * @throws InvalidParameterPointException
     * @throws InvalidParameterPointSyntaxException
     */
    public function __construct(string $fqn)
    {
        $regex = '/^([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/';

        if (!\preg_match($regex, $fqn, $matches)) {
            throw new InvalidParameterPointSyntaxException(\sprintf(
                '"%s" is not a parameter point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                $regex
            ));
        }

        list($matchedFqn, $matchedClassName, $matchedMethodName, $matchedName) = $matches;

        try {
            $reflector = new ReflectionParameter([$matchedClassName, $matchedMethodName], $matchedName);
        } catch (ReflectionException $exception) {
            throw new InvalidParameterPointException(\sprintf(
                '"%s" is not a parameter point. %s.',
                $fqn,
                $exception->getMessage()
            ));
        }

        $reflector->getDeclaringFunction()->setAccessible(true);

        $this->reflector = $reflector;
        $this->fqn = $fqn;
        $this->classFqn = $matchedClassName;
        $this->name = $matchedName;
        $this->methodName = $matchedMethodName;
        $this->position = $reflector->getPosition();
    }

    /**
     * Gets the name of the method of the point.
     *
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * Gets the position of the point.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
