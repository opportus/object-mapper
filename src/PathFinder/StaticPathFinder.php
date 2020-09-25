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

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Reflector;

/**
 * The default static path finder.
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class StaticPathFinder implements PathFinderInterface
{
    /**
     * @var RouteBuilderInterface $souteBuilder
     */
    private $routeBuilder;

    /**
     * Constructs the default path finder strategy.
     *
     * @param RouteBuilderInterface $routeBuilder
     */
    public function __construct(RouteBuilderInterface $routeBuilder)
    {
        $this->routeBuilder = $routeBuilder;
    }

    /**
     * {@inheritdoc}
     *
     * This behavior consists of guessing which is the appropriate point of the
     * source to connect to each point of the target following the rules below.
     *
     * A TargetPoint can be:
     *
     * - A public property (PropertyObjectPoint)
     * - A parameter of a public setter or a public constructor
     * (ParameterObjectPoint)
     *
     * The connectable SourcePoint can be:
     *
     * - A public property having for name the same as the target point
     * (PropertyObjectPoint)
     * - A public getter having for name `'get'.ucfirst($targetPointName)` and
     * requiring no argument (MethodObjectPoint)
     */
    public function getRoutes(SourceInterface $source, TargetInterface $target): RouteCollection
    {
        $routes = [];

        $sourceReflection = $source->getReflection();
        $targetReflection = $target->getReflection();

        try {
            $targetPointReflections = $this->getTargetPointReflections(
                $target,
                $targetReflection
            );
        } catch (ReflectionException $exception) {
            throw new InvalidOperationException(
                __METHOD__,
                $exception->getMessage()
            );
        }

        foreach ($targetPointReflections as $targetPointReflection) {
            try {
                $sourcePointReflection = $this->getSourcePointReflection(
                    $sourceReflection,
                    $targetPointReflection
                );
            } catch (ReflectionException $exception) {
                throw new InvalidOperationException(
                    __METHOD__,
                    $exception->getMessage()
                );
            }

            if (null === $sourcePointReflection) {
                continue;
            }

            $sourcePointFqn = '';
            $targetPointFqn = '';

            if ($sourcePointReflection instanceof ReflectionMethod) {
                $sourcePointFqn = \sprintf(
                    '%s.%s()',
                    $sourcePointReflection->getDeclaringClass()->getName(),
                    $sourcePointReflection->getName()
                );
            } elseif ($sourcePointReflection instanceof ReflectionProperty) {
                $sourcePointFqn = \sprintf(
                    '%s.$%s',
                    $sourcePointReflection->getDeclaringClass()->getName(),
                    $sourcePointReflection->getName()
                );
            }

            if ($targetPointReflection instanceof ReflectionParameter) {
                $targetPointFqn = \sprintf(
                    '%s.%s().$%s',
                    $targetPointReflection->getDeclaringClass()->getName(),
                    $targetPointReflection->getDeclaringFunction()->getName(),
                    $targetPointReflection->getName()
                );
            } elseif ($targetPointReflection instanceof ReflectionProperty) {
                $targetPointFqn = \sprintf(
                    '%s.$%s',
                    $targetPointReflection->getDeclaringClass()->getName(),
                    $targetPointReflection->getName()
                );
            }

            $routes[] = $this->routeBuilder
                ->setStaticSourcePoint($sourcePointFqn)
                ->setStaticTargetPoint($targetPointFqn)
                ->getRoute();
        }

        return new RouteCollection($routes);
    }

    /**
     * Gets target point reflections.
     *
     * @param TargetInterface $target
     * @param ReflectionClass $targetReflection
     * @return Reflector[]
     * @throws ReflectionException
     */
    private function getTargetPointReflections(
        TargetInterface $target,
        ReflectionClass $targetReflection
    ): array {
        $methodBlackList = [];
        $propertyBlackList = [];

        if (
            $target->isInstantiated() === false &&
            $targetReflection->hasMethod('__construct')
        ) {
            $constructorReflection = $targetReflection
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
            $targetReflection->getMethods(ReflectionMethod::IS_PUBLIC) as
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
            $targetReflection
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
     * Gets a source point to pair with the passed target point.
     *
     * @param ReflectionClass $sourceReflection
     * @param Reflector $targetPointReflection
     * @return null|Reflector
     * @throws ReflectionException
     */
    private function getSourcePointReflection(
        ReflectionClass $sourceReflection,
        Reflector $targetPointReflection
    ): ?Reflector {
        if ($sourceReflection->hasMethod(
            \sprintf('get%s', \ucfirst($targetPointReflection->getName()))
        )) {
            $methodReflection = $sourceReflection->getMethod(
                \sprintf('get%s', \ucfirst($targetPointReflection->getName()))
            );

            if (
                $methodReflection->isPublic() === true &&
                $methodReflection->getNumberOfRequiredParameters() === 0
            ) {
                return $methodReflection;
            }
        } elseif ($sourceReflection->hasProperty(
            $targetPointReflection->getName()
        )) {
            $propertyReflection = $sourceReflection->getProperty(
                $targetPointReflection->getName()
            );

            if ($propertyReflection->isPublic() === true) {
                return $propertyReflection;
            }
        }

        return null;
    }
}
