<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use ReflectionClass;

/**
 * The default path finding strategy.
 *
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class PathFindingStrategy implements PathFindingStrategyInterface
{
    /**
     * {@inheritdoc}
     *
     * This behavior consists of guessing which is the appropriate point of the source to connect to each point of the target.
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

        $targetPoints = $this->getTargetPoints($context->getTargetClassReflection(), $context->hasInstantiatedTarget());

        foreach ($targetPoints as $targetPoint) {
            $sourcePoint = $this->findSourcePoint($context->getSourceClassReflection(), $targetPoint);

            if (null !== $sourcePoint && null !== $sourcePoint->getValue($context->getSource())) {
                $routes[] = new Route($sourcePoint, $targetPoint);
            }
        }

        return new RouteCollection($routes);
    }

    /**
     * Gets the target points.
     *
     * @param ReflectionClass $targetClassReflection
     * @param bool $isTargetInstantiated
     * @return array
     */
    private function getTargetPoints(ReflectionClass $targetClassReflection, bool $isTargetInstantiated): array
    {
        $targetPoints = [];
        foreach ($targetClassReflection->getMethods() as $targetMethodReflection) {
            if ($targetMethodReflection->isPublic()) {
                if (0 === \strpos($targetMethodReflection->getName(), 'set') || (false === $isTargetInstantiated && '__construct' === $targetMethodReflection->getName())) {
                    if ($targetMethodReflection->getNumberOfParameters() > 0) {
                        foreach ($targetMethodReflection->getParameters() as $targetParameterReflection) {
                            $targetPoints[] = new ParameterPoint(\sprintf(
                                '%s.%s().$%s',
                                $targetClassReflection->getName(),
                                $targetMethodReflection->getName(),
                                $targetParameterReflection->getName()
                            ));
                        }
                    }
                }
            }
        }

        foreach ($targetClassReflection->getProperties() as $targetPropertyReflection) {
            if ($targetPropertyReflection->isPublic()) {
                $targetPoints[] = new PropertyPoint(\sprintf(
                    '%s.$%s',
                    $targetClassReflection->getName(),
                    $targetPropertyReflection->getName()
                ));
            }
        }

        return $targetPoints;
    }

    /**
     * Finds the source point to connect to the target point.
     *
     * @param ReflectionClass $sourceClassReflection
     * @param PropertyPoint|ParameterPoint $targetPoint
     * @return null|PropertyPoint|MethodPoint
     */
    private function findSourcePoint(ReflectionClass $sourceClassReflection, object $targetPoint): ?object
    {
        foreach ($sourceClassReflection->getMethods() as $sourceMethodReflection) {
            if ($sourceMethodReflection->isPublic()) {
                if ($sourceMethodReflection->getName() === \sprintf('get%s', \ucfirst($targetPoint->getName()))) {
                    if ($sourceMethodReflection->getNumberOfRequiredParameters() === 0) {
                        return new MethodPoint(\sprintf(
                            '%s.%s()',
                            $sourceClassReflection->getName(),
                            $sourceMethodReflection->getName()
                        ));
                    }
                }
            }
        }

        foreach ($sourceClassReflection->getProperties() as $sourcePropertyReflection) {
            if ($sourcePropertyReflection->isPublic()) {
                if ($sourcePropertyReflection->getName() === $targetPoint->getName()) {
                    return new PropertyPoint(\sprintf(
                        '%s.$%s',
                        $sourceClassReflection->getName(),
                        $sourcePropertyReflection->getName()
                    ));
                }
            }
        }

        return null;
    }
}
