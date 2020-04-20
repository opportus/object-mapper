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
use ReflectionException;
use ReflectionMethod;

/**
 * The method point.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class MethodPoint extends AbstractPoint
{
    public const FQN_SYNTAX_PATTERN = '/^([A-Za-z0-9\\\_]+)\.([A-Za-z0-9_]+)\(\)$/';

    /**
     * Constructs the method point.
     *
     * @param string $fqn
     * @throws InvalidArgumentException
     */
    public function __construct(string $fqn)
    {
        if (!\preg_match(self::FQN_SYNTAX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                '%s is not a method point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                self::FQN_SYNTAX_PATTERN
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        [$matchedFqn, $matchedClassName, $matchedName] = $matches;

        try {
            new ReflectionMethod($matchedClassName, $matchedName);
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a method point. %s.',
                $fqn,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        $this->fqn = $matchedFqn;
        $this->classFqn = $matchedClassName;
        $this->name = $matchedName;
    }
}