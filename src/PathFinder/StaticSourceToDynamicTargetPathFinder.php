<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinder;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;
use ReflectionMethod;
use ReflectionParameter;
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
 * - A statically non-existing property having for name the same as the property
 *   source point or `lcfirst(substr($getterSourcePoint, 3))`
 *   (`PropertyDynamicSourcePoint`)
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

    /**
     * {@inheritdoc}
     */
    protected function getReferencePointRoute(
        SourceInterface $source,
        TargetInterface $target,
        $referencePoint
    ): ?RouteInterface {
        if (false === \is_object($referencePoint)
            || (
                !$referencePoint instanceof ReflectionProperty
                && !$referencePoint instanceof ReflectionMethod
            )
        ) {
            $message = \sprintf(
                'The argument must be an object of type % or %s, got an argument of type %s.',
                ReflectionProperty::class,
                ReflectionParameter::class,
                \is_object($referencePoint) ?
                    \get_class($referencePoint) : \gettype($referencePoint)
            );

            throw new InvalidArgumentException(3, $message);
        }

        $sourcePointReflection = $referencePoint;
        $targetClassReflection = $target->getClassReflection();

        if ($sourcePointReflection instanceof ReflectionProperty) {
            $targetPointName = $sourcePointReflection->getName();
        } elseif ($sourcePointReflection instanceof ReflectionMethod) {
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

        $sourcePointFqn = $this
            ->getPointFqnFromReflection($sourcePointReflection);

        $targetPointFqn = \sprintf(
            '%s::$%s',
            $targetClassReflection->getName(),
            $targetPointName
        );

        return $this->routeBuilder
            ->setStaticSourcePoint($sourcePointFqn)
            ->setDynamicTargetPoint($targetPointFqn)
            ->getRoute();
    }
}
