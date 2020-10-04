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

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;
use ReflectionParameter;
use ReflectionProperty;
use Reflector;

/**
 * The default dynamic source to static target path finder.
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class DynamicSourceToStaticTargetPathFinder implements PathFinderInterface
{
    /**
     * @var RouteBuilderInterface $souteBuilder
     */
    private $routeBuilder;

    /**
     * Constructs the default dynamic source to static target path finder.
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

        $sourceObjectReflection = $source->getObjectReflection();
        $sourceClassReflection = $source->getClassReflection();
        $targetClassReflection = $target->getClassReflection();

        $targetPointReflections = $this->getTargetPointReflections(
            $target,
            $targetClassReflection
        );

        foreach ($targetPointReflections as $targetPointReflection) {
            $sourcePointFqn = $this->getSourcePointFqn(
                $source,
                $sourceClassReflection,
                $sourceObjectReflection,
                $targetPointReflection
            );

            if (null === $sourcePointFqn) {
                continue;
            }

            $targetPointFqn = $this->translateTargetPointReflectionToFqn(
                $targetPointReflection
            );

            $routes[] = $this->routeBuilder
                ->setDynamicSourcePoint($sourcePointFqn)
                ->setStaticTargetPoint($targetPointFqn)
                ->getRoute();
        }

        return new RouteCollection($routes);
    }

    /**
     * Gets target point reflections.
     *
     * @param  TargetInterface $target
     * @param  ReflectionClass $targetClassReflection
     * @return Reflector[]
     */
    private function getTargetPointReflections(
        TargetInterface $target,
        ReflectionClass $targetClassReflection
    ): array {
        $methodBlackList = [];
        $propertyBlackList = [];

        if (
            $target->isInstantiated() === false &&
            $targetClassReflection->hasMethod('__construct')
        ) {
            $constructorReflection = $targetClassReflection
                ->getMethod('__construct');

            foreach (
                $constructorReflection->getParameters() as
                $parameterReflection
            ) {
                $methodBlackList[] = \sprintf(
                    'set%s',
                    \ucfirst($parameterReflection->getName())
                );

                $propertyBlackList[] = $parameterReflection->getName();
            }
        }

        $targetPointReflections = [];

        foreach (
            $targetClassReflection->getMethods(ReflectionMethod::IS_PUBLIC) as
            $methodReflection
        ) {
            if (\in_array($methodReflection->getName(), $methodBlackList)) {
                continue;
            }

            if ($methodReflection->getNumberOfParameters() === 0) {
                continue;
            }

            if (
                \strpos($methodReflection->getName(), 'set') !== 0 &&
                (
                    $target->isInstantiated() ||
                    $methodReflection->getName() !== '__construct'
                )
            ) {
                continue;
            }

            foreach (
                $methodReflection->getParameters() as
                $parameterReflection
            ) {
                $targetPointReflections[] = $parameterReflection;
            }
        }

        foreach (
            $targetClassReflection
                ->getProperties(ReflectionProperty::IS_PUBLIC) as
            $propertyReflection
        ) {
            if (\in_array($propertyReflection->getName(), $propertyBlackList)) {
                continue;
            }

            $targetPointReflections[] = $propertyReflection;
        }

        return $targetPointReflections;
    }

    /**
     * Gets a target point to pair with the passed source point.
     *
     * @param  SourceInterface  $source
     * @param  ReflectionClass  $sourceClassReflection
     * @param  ReflectionObject $sourceObjectReflection
     * @param  Reflector        $targetPointReflection
     * @return null|string
     */
    private function getSourcePointFqn(
        SourceInterface $source,
        ReflectionClass $sourceClassReflection,
        ReflectionObject $sourceObjectReflection,
        Reflector $targetPointReflection
    ): ?string {
        if (!$sourceClassReflection
                ->hasProperty($targetPointReflection->getName()) &&
            $sourceObjectReflection
                ->hasProperty($targetPointReflection->getName())
        ) {
            return \sprintf(
                '%s.$%s',
                $sourceClassReflection->getName(),
                $targetPointReflection->getName()
            );
        } elseif (!$sourceClassReflection->hasMethod(\sprintf(
            'get%s',
            \ucfirst($targetPointReflection->getName())
        )) &&
            \is_callable([$source->getInstance(), \sprintf(
                'get%s',
                \ucfirst($targetPointReflection->getName())
            )])
        ) {
            return \sprintf(
                '%s.get%s()',
                $sourceClassReflection->getName(),
                \ucfirst($targetPointReflection->getName())
            );
        }

        return null;
    }

    /**
     * Translates the target point reflection to its Fully Qualified Name
     *
     * @param  Reflector                $reflection A target point reflection to
     *                                              translate to its Fully
     *                                              Qualified Name
     * @return string                               The target point Fully
     *                                              Qualified Name
     * @throws InvalidArgumentException             If the reflection is not a
     *                                              target point reflection
     */
    private function translateTargetPointReflectionToFqn(
        Reflector $reflection
    ): string {
        if ($reflection instanceof ReflectionProperty) {
            return \sprintf(
                '%s.$%s',
                $reflection->getDeclaringClass()->getName(),
                $reflection->getName()
            );
        }
        
        if ($reflection instanceof ReflectionParameter) {
            return \sprintf(
                '%s.%s().$%s',
                $reflection->getDeclaringClass()->getName(),
                $reflection->getDeclaringFunction()->getName(),
                $reflection->getName()
            );
        }

        $message = \sprintf(
            'The argument must be of type %s, got an argument of type %s.',
            \implode('|', [
                ReflectionProperty::class,
                ReflectionParameter::class,
            ]),
            get_class($reflection)
        );

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }
}
