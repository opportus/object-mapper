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
use ReflectionException;
use ReflectionParameter;

/**
 * The parameter point.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class ParameterPoint extends AbstractPoint
{
    public const FQN_SYNTAX_PATTERN = '/^([A-Za-z0-9\\\_]+).([A-Za-z0-9_]+)\(\).\$([A-Za-z0-9_]+)$/';

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
     * @throws InvalidArgumentException
     */
    public function __construct(string $fqn)
    {
        if (!\preg_match(self::FQN_SYNTAX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                '%s is not a parameter point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                self::FQN_SYNTAX_PATTERN
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        [
            $matchedFqn,
            $matchedClassName,
            $matchedMethodName,
            $matchedName
        ] = $matches;

        try {
            $reflector = new ReflectionParameter(
                [$matchedClassName, $matchedMethodName],
                $matchedName
            );
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a parameter point. %s.',
                $fqn,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        $reflector->getDeclaringFunction()->setAccessible(true);

        $this->reflector = $reflector;
        $this->fqn = $matchedFqn;
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
