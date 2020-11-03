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
 * The default static path finder.
 *
 * This behavior consists of guessing the static point of the source to
 * connect to each static point of the target following the rules below.
 *
 * A target point can be:
 *
 * - A public property (`PropertyStaticTargetPoint`)
 * - A parameter of a public setter or a public constructor
 *   (`ParameterStaticTargetPoint`)
 *
 * The connectable source point can be:
 *
 * - A public property having for name the same as the target point
 *   (`PropertyStaticSourcePoint`)
 * - A public getter having for name `'get'.ucfirst($targetPointName)` and
 *   requiring no argument (`MethodStaticSourcePoint`)
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class StaticPathFinder extends PathFinder
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function getReferencePointRoute(
        SourceInterface $source,
        TargetInterface $target,
        $referencePoint
    ): ?RouteInterface {
        $targetPointReflection = $referencePoint;
        $sourceClassReflection = $source->getClassReflection();

        if ($sourceClassReflection->hasProperty(
            $targetPointReflection->getName()
        )) {
            $propertyReflection = $sourceClassReflection->getProperty(
                $targetPointReflection->getName()
            );

            if ($propertyReflection->isPublic() === true) {
                $sourcePointReflection = $propertyReflection;
            }
        }
        
        if ($sourceClassReflection->hasMethod(
            \sprintf('get%s', \ucfirst($targetPointReflection->getName()))
        )) {
            $methodReflection = $sourceClassReflection->getMethod(
                \sprintf('get%s', \ucfirst($targetPointReflection->getName()))
            );

            if (
                $methodReflection->isPublic() === true &&
                $methodReflection->getNumberOfRequiredParameters() === 0
            ) {
                $sourcePointReflection = $methodReflection;
            }
        }
        
        if (false === isset($sourcePointReflection)) {
            return null;
        }

        $sourcePointFqn = $this
            ->getPointFqnFromReflection($sourcePointReflection);

        $targetPointFqn = $this
            ->getPointFqnFromReflection($targetPointReflection);

        return $this->routeBuilder
            ->setStaticSourcePoint($sourcePointFqn)
            ->setStaticTargetPoint($targetPointFqn)
            ->getRoute();
    }
}
