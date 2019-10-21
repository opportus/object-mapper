<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
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
     * This behavior consists of guessing which is the appropriate point of the source
     * to connect to each point of the target following the rules below.
     *
     * A TargetPoint can be:
     *
     * - A public property (PropertyPoint)
     * - A parameter of a public setter or a public constructor (ParameterPoint)
     *
     * The connectable SourcePoint can be:
     *
     * - A public property having for name the same as the target point (PropertyPoint)
     * - A public getter having for name 'get'.ucfirst($targetPointName) and requiring no argument (MethodPoint)
     */
    public function getRoutes(Context $context): RouteCollection
    {
        $routes = [];

        $targetPointReflections = $this->getConventionalTargetPointReflections($context);

        foreach ($targetPointReflections as $targetPointReflection) {
            $sourcePointReflection = $this->getConventionalSourcePointReflection($context, $targetPointReflection);

            if (null !== $sourcePointReflection) {
                if ($sourcePointReflection instanceof ReflectionMethod) {
                    $sourcePointFqn = \sprintf('%s.%s()', $sourcePointReflection->getDeclaringClass()->getName(), $sourcePointReflection->getName());
                } elseif ($sourcePointReflection instanceof ReflectionProperty) {
                    $sourcePointFqn = \sprintf('%s.$%s', $sourcePointReflection->getDeclaringClass()->getName(), $sourcePointReflection->getName());
                }

                if ($targetPointReflection instanceof ReflectionParameter) {
                    $targetPointFqn = \sprintf('%s.%s().$%s', $targetPointReflection->getDeclaringClass()->getName(), $targetPointReflection->getDeclaringFunction()->getName(), $targetPointReflection->getName());
                } elseif ($targetPointReflection instanceof ReflectionProperty) {
                    $targetPointFqn = \sprintf('%s.$%s', $targetPointReflection->getDeclaringClass()->getName(), $targetPointReflection->getName());
                }

                $routes[] = $this->routeBuilder->buildRoute($sourcePointFqn, $targetPointFqn);
            }
        }

        return new RouteCollection($routes);
    }

    /**
     * Gets conventional target point reflections.
     *
     * @param Context $context
     * @return Reflector[]
     */
    private function getConventionalTargetPointReflections(Context $context): array
    {
        $targetClassReflection = $context->getTargetClassReflection();

        $methodBlackList = [];
        $propertyBlackList = [];

        if (false === $context->hasInstantiatedTarget() && $targetClassReflection->hasMethod('__construct')) {
            foreach ($targetClassReflection->getMethod('__construct')->getParameters() as $targetConstructParameterReflection) {
                $methodBlackList[] = \sprintf('set%s', \ucfirst($targetConstructParameterReflection->getName()));
                $propertyBlackList[] = $targetConstructParameterReflection->getName();
            }
        }

        $targetPointReflections = [];

        foreach ($targetClassReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $targetMethodReflection) {
            if (\in_array($targetMethodReflection->getName(), $methodBlackList)) {
                continue;
            }

            if ($targetMethodReflection->getNumberOfParameters() === 0) {
                continue;
            }

            if (0 !== \strpos($targetMethodReflection->getName(), 'set') &&
                (true === $context->hasInstantiatedTarget() || '__construct' !== $targetMethodReflection->getName())
            ) {
                continue;
            }

            foreach ($targetMethodReflection->getParameters() as $targetParameterReflection) {
                $targetPointReflections[] = $targetParameterReflection;
            }
        }

        foreach ($targetClassReflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $targetPropertyReflection) {
            if (\in_array($targetPropertyReflection->getName(), $propertyBlackList)) {
                continue;
            }

            $targetPointReflections[] = $targetPropertyReflection;
        }

        return $targetPointReflections;
    }

    /**
     * Gets a conventional source point reflection to pair with the passed target point reflection.
     *
     * @param Context $context
     * @param ReflectionProperty|ReflectionParameter $targetPointReflection
     * @return null|ReflectionProperty|ReflectionMethod
     */
    private function getConventionalSourcePointReflection(Context $context, Reflector $targetPointReflection): ?Reflector
    {
        $sourceClassReflection = $context->getSourceClassReflection();

        if ($sourceClassReflection->hasMethod(\sprintf('get%s', \ucfirst($targetPointReflection->getName())))) {
            $sourceMethodReflection = $sourceClassReflection->getMethod(\sprintf('get%s', \ucfirst($targetPointReflection->getName())));

            if (true === $sourceMethodReflection->isPublic() &&
                0    === $sourceMethodReflection->getNumberOfRequiredParameters() &&
                null !== $sourceMethodReflection->invoke($context->getSource())
            ) {
                return $sourceMethodReflection;
            }
        } elseif ($sourceClassReflection->hasProperty($targetPointReflection->getName())) {
            $sourcePropertyReflection = $sourceClassReflection->getProperty($targetPointReflection->getName());

            if (true === $sourcePropertyReflection->isPublic() &&
                null !== $sourcePropertyReflection->getValue($context->getSource())
            ) {
                return $sourcePropertyReflection;
            }
        }

        return null;
    }
}
