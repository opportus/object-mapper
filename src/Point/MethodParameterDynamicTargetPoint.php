<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

/**
 * The method parameter dynamic target point.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodParameterDynamicTargetPoint extends TargetPoint implements DynamicTargetPointInterface
{
    private const FQN_REGEX_PATTERN = '/^~?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/';

    /**
     * @var string $methodName
     */
    private $methodName;

    /**
     * Constructs the method parameter dynamic target point.
     *
     * @param string $fqn
     * @throws InvalidArgumentException
     */
    public function __construct(string $fqn)
    {
        if (!\preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                '%s is not a method parameter dynamic target point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                self::FQN_REGEX_PATTERN
            );

            throw new InvalidArgumentException(1, $message);
        }

        [
            $matchedFqn,
            $matchedTargetFqn,
            $matchedMethodName,
            $matchedName
        ] = $matches;

        try {
            $targetClassReflection = new ReflectionClass($matchedTargetFqn);
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a method parameter dynamic target point. %s.',
                $fqn,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, $message);
        }

        if ($targetClassReflection->hasMethod($matchedMethodName)) {
            $message = \sprintf(
                '%s is not a method parameter dynamic target point since the point is statically defined.',
                $fqn
            );

            throw new InvalidArgumentException(1, $message);
        }

        $this->fqn = \sprintf('~%s', \ltrim($matchedFqn, '~'));
        $this->targetFqn = $matchedTargetFqn;
        $this->name = $matchedName;
        $this->methodName = $matchedMethodName;
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
     * {@inheritdoc}
     */
    public static function getFqnRegexPattern(): string
    {
        return self::FQN_REGEX_PATTERN;
    }
}
