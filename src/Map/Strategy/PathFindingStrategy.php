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
use ReflectionException;

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

        $targetPoints = $this->buildConventionalTargetPoints($context);

        foreach ($targetPoints as $targetPoint) {
            $sourcePoint = $this->buildConventionalSourcePoint($context, $targetPoint);

            if (null !== $sourcePoint && null !== $sourcePoint->getValue($context->getSource())) {
                $routes[] = new Route($sourcePoint, $targetPoint);
            }
        }

        return new RouteCollection($routes);
    }

    /**
     * Builds conventional target points.
     *
     * @param Context $context
     * @return array
     */
    private function buildConventionalTargetPoints(Context $context): array
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

        $targetPoints = [];

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
                $targetPoints[] = new ParameterPoint(\sprintf(
                    '%s.%s().$%s',
                    $targetClassReflection->getName(),
                    $targetMethodReflection->getName(),
                    $targetParameterReflection->getName()
                ));
            }
        }

        foreach ($targetClassReflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $targetPropertyReflection) {
            if (\in_array($targetPropertyReflection->getName(), $propertyBlackList)) {
                continue;
            }

            $targetPoints[] = new PropertyPoint(\sprintf(
                '%s.$%s',
                $targetClassReflection->getName(),
                $targetPropertyReflection->getName()
            ));
        }

        return $targetPoints;
    }

    /**
     * Builds conventional source point to connect to the passed target point.
     *
     * @param Context $context
     * @param PropertyPoint|ParameterPoint $targetPoint
     * @return null|PropertyPoint|MethodPoint
     */
    private function buildConventionalSourcePoint(Context $context, object $targetPoint): ?object
    {
        $sourceClassReflection = $context->getSourceClassReflection();

        try {
            $sourceMethodReflection = $sourceClassReflection->getMethod(\sprintf('get%s', \ucfirst($targetPoint->getName())));

            if (false === $sourceMethodReflection->isPublic() || $sourceMethodReflection->getNumberOfRequiredParameters() > 0) {
                throw ReflectionException();
            }
        } catch (ReflectionException $e) {
            try {
                $sourcePropertyReflection = $sourceClassReflection->getProperty($targetPoint->getName());
            } catch (ReflectionException $e) {
                return null;
            }

            if (false === $sourcePropertyReflection->isPublic()) {
                return null;
            }

            return new PropertyPoint(\sprintf(
                '%s.$%s',
                $sourceClassReflection->getName(),
                $sourcePropertyReflection->getName()
            ));
        }

        return new MethodPoint(\sprintf(
            '%s.%s()',
            $sourceClassReflection->getName(),
            $sourceMethodReflection->getName()
        ));
    }
}
