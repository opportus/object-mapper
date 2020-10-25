<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinder;

use Exception;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Reflector;

/**
 * The path finder.
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
abstract class PathFinder implements PathFinderInterface
{
    /**
     * @var RouteBuilderInterface $souteBuilder
     */
    protected $routeBuilder;

    /**
     * Constructs the path finder.
     *
     * @param RouteBuilderInterface $routeBuilder
     */
    public function __construct(RouteBuilderInterface $routeBuilder)
    {
        $this->routeBuilder = $routeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes(
        SourceInterface $source,
        TargetInterface $target
    ): RouteCollection {
        $routes = [];

        try {
            $referencePoints = $this->getReferencePoints($source, $target);
        } catch (Exception $exception) {
            throw new InvalidOperationException(
                __METHOD__,
                $exception->getMessage()
            );
        }

        foreach ($referencePoints as $referencePoint) {
            try {
                $route = $this->getReferencePointRoute(
                    $source,
                    $target,
                    $referencePoint
                );
            } catch (Exception $exception) {
                throw new InvalidOperationException(
                    __METHOD__,
                    $exception->getMessage()
                );
            }

            if (null === $route) {
                continue;
            }

            $routes[] = $route;
        }

        return new RouteCollection($routes);
    }

    /**
     * Gets the reference points.
     *
     * @param  SourceInterface $source
     * @param  TargetInterface $target
     * @return array
     */
    abstract protected function getReferencePoints(
        SourceInterface $source,
        TargetInterface $target
    ): array;

    /**
     * Gets the reference point route.
     *
     * @param  SourceInterface     $source
     * @param  TargetInterface     $target
     * @param  mixed               $referencePoint
     * @return null|RouteInterface
     */
    abstract protected function getReferencePointRoute(
        SourceInterface $source,
        TargetInterface $target,
        $referencePoint
    ): ?RouteInterface;

    /**
     * Gets the FQN of the point based on its passed reflection.
     *
     * @param  Reflector                $reflection A point reflection to
     *                                              translate to its Fully
     *                                              Qualified Name
     * @return string                               The point Fully Qualified
     *                                              Name
     * @throws InvalidArgumentException             If the reflection is not a
     *                                              point reflection
     */
    protected function getPointFqnFromReflection(
        Reflector $reflection
    ): string {
        if ($reflection instanceof ReflectionProperty) {
            return \sprintf(
                '%s::$%s',
                $reflection->getDeclaringClass()->getName(),
                $reflection->getName()
            );
        }
        
        if ($reflection instanceof ReflectionMethod) {
            return \sprintf(
                '%s::%s()',
                $reflection->getDeclaringClass()->getName(),
                $reflection->getName()
            );
        }
        
        if ($reflection instanceof ReflectionParameter) {
            return \sprintf(
                '%s::%s()::$%s',
                $reflection->getDeclaringClass()->getName(),
                $reflection->getDeclaringFunction()->getName(),
                $reflection->getName()
            );
        }

        $message = \sprintf(
            'The argument must be of type %s, got an argument of type %s.',
            \implode('|', [
                ReflectionProperty::class,
                ReflectionMethod::class,
                ReflectionParameter::class,
            ]),
            get_class($reflection)
        );

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }
}
