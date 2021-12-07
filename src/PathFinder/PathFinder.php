<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinder;

use Exception;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\StaticTargetPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
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
     * @var bool $recursiveMode
     */
    protected $recursiveMode;

    /**
     * Constructs the path finder.
     *
     * @param RouteBuilderInterface $routeBuilder
     * @param bool $recursiveMode
     */
    public function __construct(
        RouteBuilderInterface $routeBuilder,
        bool $recursiveMode = true
    ) {
        $this->routeBuilder = $routeBuilder;
        $this->recursiveMode = $recursiveMode;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function getRoutes(
        SourceInterface $source,
        TargetInterface $target
    ): RouteCollection {
        $routes = [];

        try {
            $referencePoints = $this->getReferencePoints($source, $target);
        } catch (Exception $exception) {
            throw new InvalidOperationException('', 0, $exception);
        }

        foreach ($referencePoints as $referencePoint) {
            try {
                $route = $this->getReferencePointRoute(
                    $source,
                    $target,
                    $referencePoint
                );
            } catch (Exception $exception) {
                throw new InvalidOperationException('', 0, $exception);
            }

            if (null === $route) {
                continue;
            }

            $routes[] = $route;
        }

        $routes = new RouteCollection($routes);

        if (true === $this->recursiveMode) {
            $routes = $this->addRecursionCheckPointToRoutes($routes, $source);
        }

        return $routes;
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
     * @param  SourceInterface          $source
     * @param  TargetInterface          $target
     * @param  mixed                    $referencePoint
     * @return null|RouteInterface
     * @throws InvalidArgumentException
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

        throw new InvalidArgumentException(1, $message);
    }

    /**
     * @param RouteCollection $routes
     * @param SourceInterface $source
     * @return RouteCollection
     * @throws InvalidArgumentException
     * @throws InvalidOperationException
     */
    protected function addRecursionCheckPointToRoutes(
        RouteCollection $routes,
        SourceInterface $source
    ): RouteCollection {
        if (false === $this->recursiveMode) {
            return $routes;
        }

        $newRoutes = [];

        /** @var RouteInterface $route */
        foreach ($routes as $route) {
            $sourcePoint = $route->getSourcePoint();
            $targetPoint = $route->getTargetPoint();

            $sourcePointValue = $source->getPointValue($sourcePoint);

            $recursionTargetFqn = $this->findRecursionTargetFqn(
                $targetPoint,
                $sourcePointValue
            );

            if ($recursionTargetFqn) {
                $routeBuilder = $this->routeBuilder
                    ->setSourcePoint($sourcePoint->getFqn())
                    ->setTargetPoint($targetPoint->getFqn());

                if (\is_object($sourcePointValue)) {
                    $route = $routeBuilder
                        ->addRecursionCheckPoint(
                            \get_class($sourcePointValue),
                            $recursionTargetFqn,
                            \sprintf(
                                '%s::$%s',
                                $targetPoint->getTargetFqn(),
                                $targetPoint->getName()
                            )
                        )
                        ->getRoute();
                } elseif (\is_array($sourcePointValue)) {
                    $route = $routeBuilder
                        ->addIterableRecursionCheckPoint(
                            \get_class(\array_pop($sourcePointValue)),
                            $recursionTargetFqn,
                            \sprintf(
                                '%s::$%s',
                                $targetPoint->getTargetFqn(),
                                $targetPoint->getName()
                            )
                        )
                        ->getRoute();
                }
            }

            $newRoutes[] = $route;
        }

        return new RouteCollection($newRoutes);
    }

    /**
     * @param TargetPointInterface $targetPoint
     * @param $sourcePointValue
     * @return string
     */
    private function findRecursionTargetFqn(
        TargetPointInterface $targetPoint,
        $sourcePointValue
    ): string {
        $recursionTargetFqn = '';

        if (!$targetPoint instanceof StaticTargetPointInterface) {
            return $recursionTargetFqn;
        }

        $targetPointValueType = $this->guessTargetPointValueType($targetPoint, $sourcePointValue);

        if (!$targetPointValueType) {
            return $recursionTargetFqn;
        }

        if (\is_object($sourcePointValue)) {
            if($targetPointValueType === \get_class($sourcePointValue)) {
                return $recursionTargetFqn;
            }

            $recursionTargetFqn = $targetPointValueType;
        } elseif (\is_array($sourcePointValue)) {
            $sourcePointValueIterableObjectsType = '';

            foreach ($sourcePointValue as $value) {
                if (!\is_object($value)) {
                    return $recursionTargetFqn;
                }

                $valueClass = \get_class($value);

                if ('' === $sourcePointValueIterableObjectsType) {
                    $sourcePointValueIterableObjectsType = $valueClass;
                } elseif ($valueClass !== $sourcePointValueIterableObjectsType) {
                    return $recursionTargetFqn;
                } elseif ($valueClass === $targetPointValueType) {
                    return $recursionTargetFqn;
                }

                $recursionTargetFqn = $targetPointValueType;
            }
        }

        return $recursionTargetFqn;
    }

    /**
     * @param StaticTargetPointInterface $targetPoint
     * @param $sourcePointValue
     * @return string
     */
    private function guessTargetPointValueType(
        StaticTargetPointInterface $targetPoint,
        $sourcePointValue
    ): string {
        $targetPointValueType = '';

        if (\is_object($sourcePointValue)) {
            $targetPointValueTypes = $targetPoint->getValuePhpTypes();

            if (
                \count($targetPointValueTypes) === 0
                || \count($targetPointValueTypes) > 2
                || \count($targetPointValueTypes) === 2
                && !\in_array('null', $targetPointValueTypes)
            ) {
                return $targetPointValueType;
            }

            foreach ($targetPointValueTypes as $type) {
                if (
                    'null' !== $type
                    && \class_exists($type)
                ) {
                    $targetPointValueType = $type;
                    break;
                }
            }
        } elseif (\is_array($sourcePointValue)) {
            $targetPointValueTypes = $targetPoint->getValuePhpDocTypes();

            if (
                \count($targetPointValueTypes) === 0
                || \count($targetPointValueTypes) > 2
                || \count($targetPointValueTypes) === 2
                && !\in_array('null', $targetPointValueTypes)
            ) {
                return $targetPointValueType;
            }

            foreach ($targetPointValueTypes as $type) {
                if (
                    'null' !== $type
                    && \strlen($type) - 2 === \strrpos($type, '[]')
                    && \class_exists($type = \str_replace('[]', '', $type))
                ) {
                    $targetPointValueType = $type;
                    break;
                }
            }
        }

        return $targetPointValueType;
    }
}
