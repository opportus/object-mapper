<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\PathFinder;

use Opportus\ObjectMapper\PathFinder\DynamicSourceToStaticTargetPathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinder;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * The dynamic source to static target path finder test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class DynamicSourceToStaticTargetPathFinderTest extends PathFinderTest
{
    protected function createPathFinder(): PathFinder
    {
        return new DynamicSourceToStaticTargetPathFinder(
            $this->createRouteBuilder()
        );
    }

    protected function getReferencePoints(
        SourceInterface $source,
        TargetInterface $target
    ): array {
        $targetClassReflection = $target->getClassReflection();

        $methodBlackList = [];
        $propertyBlackList = [];

        if (
            $target->getInstance() === null &&
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

            if (
                \strpos($methodReflection->getName(), 'set') !== 0 &&
                (
                    $target->getInstance() ||
                    $methodReflection->getName() !== '__construct'
                )
            ) {
                continue;
            }

            foreach (
                $methodReflection->getParameters() as
                $parameterReflection
            ) {
                $propertyBlackList[] = $parameterReflection->getName();

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


    protected function getReferencePointRoute(
        SourceInterface $source,
        TargetInterface $target,
        $referencePoint
    ): ?RouteInterface {
        $targetPointReflection = $referencePoint;
        $sourceClassReflection = $source->getClassReflection();
        $sourceObjectReflection = $source->getObjectReflection();

        if ($sourceClassReflection
                ->hasProperty($targetPointReflection->getName()) ||
            !$sourceObjectReflection
                ->hasProperty($targetPointReflection->getName())
        ) {
            return null;
        }

        $sourcePointFqn = \sprintf(
            '~%s::$%s',
            $sourceClassReflection->getName(),
            $targetPointReflection->getName()
        );

        if ($targetPointReflection instanceof ReflectionProperty) {
            $targetPointFqn = \sprintf(
                '#%s::$%s',
                $targetPointReflection->getDeclaringClass()->getName(),
                $targetPointReflection->getName()
            );
        } elseif ($targetPointReflection instanceof ReflectionParameter) {
            $targetPointFqn = \sprintf(
                '#%s::%s()::$%s',
                $targetPointReflection->getDeclaringClass()->getName(),
                $targetPointReflection->getDeclaringFunction()->getName(),
                $targetPointReflection->getName()
            );
        }

        return $this->createRouteBuilder()
            ->setDynamicSourcePoint($sourcePointFqn)
            ->setStaticTargetPoint($targetPointFqn)
            ->getRoute();
    }
}
