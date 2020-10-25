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

use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;
use ReflectionMethod;
use ReflectionProperty;

/**
 * The default static source to dynamic target path finder.
 *
 * This behavior consists of guessing the dynamic point of the target to
 * connect to each static point of the source following the rules below.
 *
 * A source point can be:
 *
 * - A public property (`PropertyStaticSourcePoint`)
 * - A public getter (`MethodStaticSourcePoint`)
 *
 * The connectable target point can be:
 *
 * - A statically non-existing property having for name the same as the source
 *   point (`PropertyDynamicSourcePoint`)
 * - A parameter of a statically non-existing setter having for name the same as
 *   the source point (`ParameterDynamicTargetPoint`)
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class StaticSourceToDynamicTargetPathFinder extends PathFinder
{
    /**
     * {@inheritdoc}
     */
    protected function getReferencePoints(
        SourceInterface $source,
        TargetInterface $target
    ): array {
        $sourceClassReflection = $source->getClassReflection();
        $sourcePointReflections = [];

        foreach (
            $sourceClassReflection->getMethods(ReflectionMethod::IS_PUBLIC) as
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
            $sourceClassReflection->getProperties(ReflectionProperty::IS_PUBLIC) as
            $propertyReflection
        ) {
            $sourcePointReflections[] = $propertyReflection;
        }

        return $sourcePointReflections;
    }

    /**
     * {@inheritdoc}
     */
    protected function getReferencePointRoute(
        SourceInterface $source,
        TargetInterface $target,
        $referencePoint
    ): ?RouteInterface {
        $sourcePointReflection = $referencePoint;
        $targetClassReflection = $target->getClassReflection();

        if ($sourcePointReflection instanceof ReflectionProperty) {
            if (!$targetClassReflection->hasProperty($sourcePointReflection->getName())) {
                $targetPointFqn = \sprintf(
                    '%s::$%s',
                    $targetClassReflection->getName(),
                    $sourcePointReflection->getName()
                );
            }
        } elseif ($sourcePointReflection instanceof ReflectionMethod) {
            $targetPointName = \lcfirst(\str_replace(
                'get',
                '',
                $sourcePointReflection->getName()
            ));

            if (!$targetClassReflection->hasProperty($targetPointName)) {
                $targetPointFqn = \sprintf(
                    '%s::$%s',
                    $targetClassReflection->getName(),
                    $targetPointName
                );
            }
        }
        
        if (false === isset($targetPointFqn)) {
            return null;
        }

        $sourcePointFqn = $this
            ->getPointFqnFromReflection($sourcePointReflection);

        return $this->routeBuilder
            ->setStaticSourcePoint($sourcePointFqn)
            ->setDynamicTargetPoint($targetPointFqn)
            ->getRoute();
    }
}
