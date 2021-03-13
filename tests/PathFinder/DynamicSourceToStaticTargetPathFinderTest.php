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
class DynamicSourceToStaticTargetPathFinderTest extends StaticPathFinderTest
{
    protected function createPathFinder(): PathFinder
    {
        return new DynamicSourceToStaticTargetPathFinder(
            $this->createRouteBuilder()
        );
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
