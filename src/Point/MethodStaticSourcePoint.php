<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;

/**
 * The method static source point.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodStaticSourcePoint extends SourcePoint implements StaticSourcePointInterface
{
    private const FQN_REGEX_PATTERN = '/^#?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/';

    /**
     * @var string[] $valuePhpTypes
     */
    private $valuePhpTypes;

    /**
     * @var string[] $valuePhpDocTypes
     */
    private $valuePhpDocTypes;

    /**
     * Constructs the method static source point.
     *
     * @param string $fqn
     * @throws InvalidArgumentException
     */
    public function __construct(string $fqn)
    {
        if (!\preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                '%s is not a method static source point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                self::FQN_REGEX_PATTERN
            );

            throw new InvalidArgumentException(1, $message);
        }

        [$matchedFqn, $matchedSourceFqn, $matchedName] = $matches;

        try {
            $reflection = new ReflectionMethod($matchedSourceFqn, $matchedName);
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a method static source point. %s.',
                $fqn,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, $message);
        }

        if (0 !== $reflection->getNumberOfRequiredParameters()) {
            $message = \sprintf(
                '%s is not a method static source point because such cannot have required parameters.',
                $fqn
            );

            throw new InvalidArgumentException(1, $message);
        }

        $this->fqn = \sprintf('#%s', \ltrim($matchedFqn, '#'));
        $this->sourceFqn = $matchedSourceFqn;
        $this->name = $matchedName;
        $this->valuePhpTypes = $this->resolveValuePhpTypes($reflection);
        $this->valuePhpDocTypes = $this->resolveValuePhpDocTypes($reflection);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFqnRegexPattern(): string
    {
        return self::FQN_REGEX_PATTERN;
    }

    /**
     * {@inheritdoc}
     */
    public function getValuePhpTypes(): array
    {
        return $this->valuePhpTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getValuePhpDocTypes(): array
    {
        return $this->valuePhpDocTypes;
    }

    /**
     * @param ReflectionMethod $reflection
     * @return string[]
     */
    private function resolveValuePhpTypes(ReflectionMethod $reflection): array
    {
        if (null === $reflection->getReturnType()) {
            return [];
        }

        $types = [];
        foreach (\explode('|', $reflection->getReturnType()->getName()) as $type) {
            if (0 === \strpos($type, '?')) {
                $types['null'] = 'null';
                $types[\ltrim($type, '?')] = \ltrim($type, '?');

                continue;
            }

            $types[$type] = $type;
        }

        return \array_values($types);
    }

    /**
     * @param ReflectionMethod $reflection
     * @return string[]
     */
    private function resolveValuePhpDocTypes(ReflectionMethod $reflection): array
    {
        \preg_match('/@return ([^\r\n]+)/s', $reflection->getDocComment(), $matches);

        if (false === isset($matches[1])) {
            return [];
        }

        return \explode('|', $matches[1]);
    }
}
