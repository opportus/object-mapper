<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinding;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * The overloaded path finding.
 *
 * @package Opportus\ObjectMapper\PathFinding
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class OverloadedPathFinding implements PathFindingInterface
{
    /**
     * @var RouteBuilderInterface $souteBuilder
     */
    private $routeBuilder;

    /**
     * Constructs the overloaded path finding strategy.
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
    public function getRoutes(Source $source, Target $target): RouteCollection
    {
        $routes = [];

        $sourceReflection = $source->getReflection();
        $targetReflection = $target->getReflection();

        $sourcePointReflections = $this->getSourcePointReflections(
            $sourceReflection
        );

        foreach ($sourcePointReflections as $sourcePointReflection) {
            $targetPointFqn = $this->getTargetPointFqn(
                $targetReflection,
                $sourcePointReflection
            );

            if (null === $targetPointFqn) {
                continue;
            }

            if ($sourcePointReflection instanceof ReflectionProperty) {
                $sourcePointFqn = \sprintf(
                    '%s.$%s',
                    $sourcePointReflection->getDeclaringClass()->getName(),
                    $sourcePointReflection->getName()
                );
            } elseif ($sourcePointReflection instanceof ReflectionMethod) {
                $sourcePointFqn = \sprintf(
                    '%s.%s()',
                    $sourcePointReflection->getDeclaringClass()->getName(),
                    $sourcePointReflection->getName()
                );
            }

            $routes[] = $this->routeBuilder
                ->setOverloadedSourcePoint($sourcePointFqn)
                ->setOverloadedTargetPoint($targetPointFqn)
                ->getRoute();
        }

        return new RouteCollection($routes);
    }

    /**
     * Gets source point reflections.
     *
     * @param ReflectionClass $sourceReflection
     * @return Reflector[]
     */
    private function getSourcePointReflections(
        ReflectionClass $sourceReflection
    ): array {
        $sourcePointReflections = [];

        foreach (
            $sourceReflection->getMethods(ReflectionMethod::IS_PUBLIC) as
            $methodReflection
        ) {
            if ($methodReflection->getNumberOfParameters() !== 0) {
                continue;
            }

            if (\strpos($methodReflection->getName(), 'get') !== 0) {
                continue;
            }

            $sourcePointReflections[] = $methodReflection;
        }

        foreach (
            $sourceReflection->getProperties(ReflectionProperty::IS_PUBLIC) as
            $propertyReflection
        ) {
            $sourcePointReflections[] = $propertyReflection;
        }

        return $sourcePointReflections;
    }

    /**
     * Gets a target point to pair with the passed source point.
     *
     * @param ReflectionClass $targetReflection
     * @param Reflector $sourcePointReflection
     * @return null|string
     */
    private function getTargetPointFqn(
        ReflectionClass $targetReflection,
        Reflector $sourcePointReflection
    ): ?string {
        if ($sourcePointReflection instanceof ReflectionProperty) {
            if (!$targetReflection->hasProperty($sourcePointReflection->getName())) {
                return \sprintf(
                    '%s.$%s',
                    $targetReflection->getName(),
                    $sourcePointReflection->getName()
                );
            }
        } elseif ($sourcePointReflection instanceof ReflectionMethod) {
            $targetPointName = \lcfirst(\str_replace(
                'get',
                '',
                $sourcePointReflection->getName()
            ));

            if (!$targetReflection->hasProperty($targetPointName)) {
                return \sprintf(
                    '%s.$%s',
                    $targetReflection->getName(),
                    $targetPointName
                );
            }
        }

        return null;
    }
}
