<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use ReflectionClass;

/**
 * The context.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class Context
{
    /**
     * @var object $source
     */
    private $source;

    /**
     * @var object|string $target
     */
    private $target;

    /**
     * Constructs the context.
     *
     * @param object $source
     * @param object|string $target
     * @throws InvalidArgumentException
     */
    public function __construct(object $source, $target)
    {
        if (!\is_string($target) && !\is_object($target)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "target" passed to "%s" is invalid. Expecting an argument of type "object" or "string", got an argument of type "%s".',
                __METHOD__,
                \gettype($target)
            ));
        }

        if (\is_string($target) && !\class_exists($target)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "target" passed to "%s" is invalid. Expecting an argument of type "string" to be a Fully Qualified Name of a class, class "%s" does not exist.',
                __METHOD__,
                $target
            ));
        }

        $this->source = $source;
        $this->target = $target;
    }

    /**
     * Gets the source.
     *
     * @return object
     */
    public function getSource(): object
    {
        return $this->source;
    }

    /**
     * Gets the target.
     *
     * @return object|string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Checks whether this has the target instantiated.
     *
     * @return bool
     */
    public function hasInstantiatedTarget(): bool
    {
        return \is_object($this->target);
    }

    /**
     * Gets the source class Fully Qualified Name.
     *
     * @return string
     */
    public function getSourceClassFqn(): string
    {
        return \get_class($this->source);
    }

    /**
     * Gets the target class Fully Qualified Name.
     *
     * @return string
     */
    public function getTargetClassFqn(): string
    {
        return $this->hasInstantiatedTarget() ? \get_class($this->target) : $this->target;
    }

    /**
     * Gets the source class reflection.
     *
     * @return ReflectionClass
     */
    public function getSourceClassReflection(): ReflectionClass
    {
        return new ReflectionClass($this->getSourceClassFqn());
    }

    /**
     * Gets the target class reflection.
     *
     * @return ReflectionClass
     */
    public function getTargetClassReflection(): ReflectionClass
    {
        return new ReflectionClass($this->getTargetClassFqn());
    }
}
