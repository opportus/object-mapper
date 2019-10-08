<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Filter\FilterInterface;
use Opportus\ObjectMapper\Map\Map;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Route\Route;
use ReflectionClass;

/**
 * The context.
 *
 * This represents the context of a mapping.
 * This contains arguments that are injected to `ObjectMapper::map(object $source, $target, ?Map $map): ?object` with their meta information.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <opportus@gmail.com>
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
     * @var Map $map
     */
    private $map;

    /**
     * Constructs the context.
     *
     * @param object $source
     * @param object|string $target
     * @param Map $map
     * @throws InvalidArgumentException
     */
    public function __construct(object $source, $target, Map $map)
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
        $this->map = $map;
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
     * Gets the map.
     *
     * @return Map
     */
    public function getMap(): Map
    {
        return $this->map;
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

    /**
     * Gets the routes.
     *
     * @return RouteCollection
     */
    public function getRoutes(): RouteCollection
    {
        return $this->map->getRoutes($this);
    }

    /**
     * Gets the filter on the passed route.
     *
     * @param Route $route
     * @return null|FilterInterface
     */
    public function getFilterOnRoute(Route $route): ?FilterInterface
    {
        return $this->map->getFilterOnRoute($route);
    }

    /**
     * Gets the Fully Qualified Name of the path finding strategy.
     *
     * @return string
     */
    public function getPathFindingStrategyFqn(): string
    {
        return $this->map->getPathFindingStrategyFqn();
    }
}
