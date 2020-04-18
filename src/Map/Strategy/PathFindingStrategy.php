<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Map\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Reflector;

/**
 * The default path finding strategy.
 *
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class PathFindingStrategy implements PathFindingStrategyInterface
{
    /**
     * @var RouteBuilderInterface $souteBuilder
     */
    private $routeBuilder;

    /**
     * Constructs the default path finding strategy.
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
     * - A public property (PropertyPoint)
     * - A parameter of a public setter or a public constructor (ParameterPoint)
     *
     * The connectable SourcePoint can be:
     *
     * - A public property having for name the same as the target point
     * (PropertyPoint)
     * - A public getter having for name `'get'.ucfirst($targetPointName)` and
     * requiring no argument (MethodPoint)
     */
    public function getRoutes(Source $source, Target $target): RouteCollection
    {
        $routes = [];

        try {
            $targetPointReflections = $this->getTargetPointReflections($target);
        } catch (ReflectionException $exception) {
            throw new InvalidOperationException(
                __METHOD__,
                $exception->getMessage()
            );
        }

        foreach ($targetPointReflections as $targetPointReflection) {
            try {
                $sourcePointReflection = $this->getSourcePointReflection(
                    $source,
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

            $routes[] = $this->routeBuilder->buildRoute(
                $sourcePointFqn,
                $targetPointFqn,
                new CheckPointCollection()
            );
        }

        return new RouteCollection($routes);
    }

    /**
     * Gets target point reflections.
     *
     * @param Target $target
     * @return Reflector[]
     * @throws ReflectionException
     */
    private function getTargetPointReflections(Target $target): array
    {
        $targetClassReflection = $target->getClassReflection();

        $methodBlackList = [];
        $propertyBlackList = [];

        if (
            $target->isInstantiated() === false &&
            $targetClassReflection->hasMethod('__construct')
        ) {
            $targetConstructorReflection = $targetClassReflection
                ->getMethod('__construct');

            foreach (
                $targetConstructorReflection->getParameters() as
                $targetConstructorParameterReflection
            ) {
                $methodBlackList[] = \sprintf(
                    'set%s',
                    \ucfirst($targetConstructorParameterReflection->getName())
                );

                $propertyBlackList[] = $targetConstructorParameterReflection
                    ->getName();
            }
        }

        $targetPointReflections = [];

        foreach (
            $targetClassReflection->getMethods(ReflectionMethod::IS_PUBLIC) as
            $targetMethodReflection
        ) {
            if (\in_array(
                $targetMethodReflection->getName(),
                $methodBlackList
            )) {
                continue;
            }

            if ($targetMethodReflection->getNumberOfParameters() === 0) {
                continue;
            }

            if (
                \strpos($targetMethodReflection->getName(), 'set') !== 0 &&
                (
                    $target->isInstantiated() === true ||
                    $targetMethodReflection->getName() !== '__construct'
                )
            ) {
                continue;
            }

            foreach (
                $targetMethodReflection->getParameters() as
                $targetParameterReflection
            ) {
                $targetPointReflections[] = $targetParameterReflection;
            }
        }

        foreach (
            $targetClassReflection
                ->getProperties(ReflectionProperty::IS_PUBLIC) as
            $targetPropertyReflection
        ) {
            if (\in_array(
                $targetPropertyReflection->getName(),
                $propertyBlackList
            )) {
                continue;
            }

            $targetPointReflections[] = $targetPropertyReflection;
        }

        return $targetPointReflections;
    }

    /**
     * Gets a source point to pair with the passed target point.
     *
     * @param Source $source
     * @param Reflector $targetPointReflection
     * @return null|Reflector
     * @throws ReflectionException
     */
    private function getSourcePointReflection(
        Source $source,
        Reflector $targetPointReflection
    ): ?Reflector {
        $sourceClassReflection = $source->getClassReflection();

        if ($sourceClassReflection->hasMethod(
            \sprintf('get%s', \ucfirst($targetPointReflection->getName()))
        )) {
            $sourceMethodReflection = $sourceClassReflection->getMethod(
                \sprintf('get%s', \ucfirst($targetPointReflection->getName()))
            );

            if (
                $sourceMethodReflection->isPublic() === true &&
                $sourceMethodReflection->getNumberOfRequiredParameters() === 0&&
                $sourceMethodReflection->invoke($source->getInstance()) !== null
            ) {
                return $sourceMethodReflection;
            }
        } elseif ($sourceClassReflection->hasProperty(
            $targetPointReflection->getName()
        )) {
            $sourcePropertyReflection = $sourceClassReflection
                ->getProperty($targetPointReflection->getName());

            if (
                $sourcePropertyReflection->isPublic() === true &&
                $sourcePropertyReflection
                    ->getValue($source->getInstance()) !== null
            ) {
                return $sourcePropertyReflection;
            }
        }

        return null;
    }
}
