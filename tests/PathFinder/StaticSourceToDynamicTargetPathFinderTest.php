<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\PathFinder;

use Opportus\ObjectMapper\PathFinder\PathFinder;
use Opportus\ObjectMapper\PathFinder\StaticSourceToDynamicTargetPathFinder;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;
use ReflectionMethod;
use ReflectionProperty;

/**
 * The static source to dynamic target path finder test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class StaticSourceToDynamicTargetPathFinderTest extends PathFinderTest
{
    protected function createPathFinder(): PathFinder
    {
        return new StaticSourceToDynamicTargetPathFinder(
            $this->createRouteBuilder()
        );
    }

    protected function getReferencePoints(
        SourceInterface $source,
        TargetInterface $target
    ): array {
        $sourceClassReflection = $source->getClassReflection();

        $propertyBlackList = [];
        $sourcePointReflections = [];

        foreach (
            $sourceClassReflection->getMethods(ReflectionMethod::IS_PUBLIC) as
            $methodReflection
        ) {
            if (\strpos($methodReflection->getName(), 'get') !== 0 &&
                \strpos($methodReflection->getName(), 'is') !== 0
            ) {
                continue;
            }

            if ($methodReflection->getNumberOfRequiredParameters() !== 0) {
                continue;
            }

            if (\strpos($methodReflection->getName(), 'get') === 0) {
                $propertyBlackList[] = \lcfirst(\substr($methodReflection->getName(), 3));
            } elseif (\strpos($methodReflection->getName(), 'is') === 0) {
                $propertyBlackList[] = \lcfirst(\substr($methodReflection->getName(), 2));
            }

            $sourcePointReflections[] = $methodReflection;
        }

        foreach (
            $sourceClassReflection->getProperties(ReflectionProperty::IS_PUBLIC) as
            $propertyReflection
        ) {
            if (\in_array($propertyReflection->getName(), $propertyBlackList)) {
                continue;
            }

            $sourcePointReflections[] = $propertyReflection;
        }

        return $sourcePointReflections;
    }

    protected function getReferencePointRoute(
        SourceInterface $source,
        TargetInterface $target,
        $referencePoint
    ): ?RouteInterface {
        $sourcePointReflection = $referencePoint;
        $targetClassReflection = $target->getClassReflection();

        if ($sourcePointReflection instanceof ReflectionProperty) {
            $targetPointName = $sourcePointReflection->getName();
        } elseif ($sourcePointReflection instanceof ReflectionMethod) {
            $targetPointName = \lcfirst(\substr(
                $sourcePointReflection->getName(),
                3
            ));
            if (\strpos($sourcePointReflection->getName(), 'get') === 0) {
                $targetPointName = \lcfirst(\substr(
                    $sourcePointReflection->getName(),
                    3
                ));
            } elseif (\strpos($sourcePointReflection->getName(), 'is') === 0) {
                $targetPointName = \lcfirst(\substr(
                    $sourcePointReflection->getName(),
                    2
                ));
            }
        }

        if ($targetClassReflection->hasProperty($targetPointName)) {
            return null;
        }

        if ($sourcePointReflection instanceof ReflectionProperty) {
            $sourcePointFqn = \sprintf(
                '#%s::$%s',
                $sourcePointReflection->getDeclaringClass()->getName(),
                $sourcePointReflection->getName()
            );
        } elseif ($sourcePointReflection instanceof ReflectionMethod) {
            $sourcePointFqn = \sprintf(
                '#%s::%s()',
                $sourcePointReflection->getDeclaringClass()->getName(),
                $sourcePointReflection->getName()
            );
        }

        $targetPointFqn = \sprintf(
            '~%s::$%s',
            $targetClassReflection->getName(),
            $targetPointName
        );

        return $this->createRouteBuilder()
            ->setStaticSourcePoint($sourcePointFqn)
            ->setDynamicTargetPoint($targetPointFqn)
            ->getRoute();
    }
}
