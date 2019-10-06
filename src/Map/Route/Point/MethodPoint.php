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
use Opportus\ObjectMapper\Exception\InvalidOperationException;
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

    const SYNTAX_PATTERN = '/^([A-Za-z0-9\\\_]+).([A-Za-z0-9_]+)\(\)$/';

    /**
     * @var ReflectionMethod $reflector
     */
    private $reflector;

    /**
     * Constructs the method point.
     *
     * @param string $fqn
     * @throws InvalidMethodPointException
     * @throws InvalidMethodPointSyntaxException
     */
    public function __construct(string $fqn)
    {
        if (!\preg_match(self::SYNTAX_PATTERN, $fqn, $matches)) {
            throw new InvalidMethodPointSyntaxException(\sprintf(
                '"%s" is not a method point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                self::SYNTAX_PATTERN
            ));
        }

        list($matchedFqn, $matchedClassName, $matchedName) = $matches;

        try {
            $reflector = new ReflectionMethod($matchedClassName, $matchedName);
        } catch (ReflectionException $exception) {
            throw new InvalidMethodPointException(\sprintf(
                '"%s" is not a method point. %s.',
                $fqn,
                $exception->getMessage()
            ));
        }

        if ($reflector->getNumberOfRequiredParameters() > 0) {
            throw new InvalidMethodPointException(\sprintf(
                '"%s" is not a method point as such cannot have required parameters.',
                $fqn
            ));
        }

        $reflector->setAccessible(true);

        $this->reflector = $reflector;
        $this->fqn = $matchedFqn;
        $this->classFqn = $matchedClassName;
        $this->name = $matchedName;
    }

    /**
     * Gets the point value from the passed object.
     *
     * @param object $object
     * @return mixed
     * @throws InvalidOperationException
     */
    public function getValue(object $object)
    {
        try {
            return $this->reflector->invoke($object);
        } catch (ReflectionException $exception) {
            throw new InvalidOperationException(\sprintf(
                'Invalid "%s" operation. %s',
                __METHOD__,
                $exception->getMessage()
            ));
        }
    }
}
