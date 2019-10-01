<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\NotSupportedContextException;
use Opportus\ObjectMapper\Map\Map;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Route;

/**
 * The object mapper.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class ObjectMapper implements ObjectMapperInterface
{
    /**
     * @var MapBuilderInterface $mapBuilder
     */
    private $mapBuilder;

    /**
     * Constructs the object mapper.
     *
     * @param MapBuilderInterface $mapBuilder
     */
    public function __construct(MapBuilderInterface $mapBuilder)
    {
        $this->mapBuilder = $mapBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapBuilder(): MapBuilderInterface
    {
        return $this->mapBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function map(object $source, $target, ?Map $map = null): ?object
    {
        $map = $map ?? $this->mapBuilder->buildMap(true);

        $context = new Context($source, $target, $map);

        // Returns NULL if nothing to do...
        if (false === $context->hasRoutes()) {
            return null;
        }

        $targetClassReflection = $context->getTargetClassReflection();

        // Instantiates the target...
        if (false === $context->hasInstantiatedTarget()) {
            $targetConstructorParameterPointValues = $this->getTargetConstructorParameterPointValues($context);

            if ($targetConstructorParameterPointValues) {
                // Invokes target constructor...
                $target = $targetClassReflection->newInstanceArgs($targetConstructorParameterPointValues);
            } else {
                $target = $targetClassReflection->newInstance();
            }

            $context = new Context($source, $target, $map);
        }


        $targetParameterPointValues = $this->getTargetParameterPointValues($context);
        $targetPropertyPointValues  = $this->getTargetPropertyPointValues($context);
        $targetPropertyPoints       = $this->getTargetPropertyPoints($context);

        // Invokes target methods...
        foreach ($targetParameterPointValues as $methodName => $methodArguments) {
            $targetClassReflection->getMethod($methodName)->invokeArgs($target, $methodArguments);
        }

        // Sets target properties...
        foreach ($targetPropertyPoints as $propertyName => $targetPropertyPoint) {
            $targetPropertyPoint->setValue($target, $targetPropertyPointValues[$propertyName]);
        }

        return $target;
    }

    /**
     * Gets target constructor parameter point values.
     *
     * @param Context $context
     * @return array
     */
    private function getTargetConstructorParameterPointValues(Context $context): array
    {
        $routes = $context->getRoutes();
        $targetConstructorParameterPointValues = [];

        foreach ($routes as $route) {
            $targetPoint = $route->getTargetPoint();

            if (!$targetPoint instanceof ParameterPoint || '__construct' !== $targetPoint->getMethodName()) {
                continue;
            }

            $targetPointValue = $this->getTargetPointValue($context, $route);

            $targetConstructorParameterPointValues[$targetPoint->getPosition()] = $targetPointValue;
        }

        return $targetConstructorParameterPointValues;
    }

    /**
     * Gets target parameter point values.
     *
     * @param Context $context
     * @return array
     */
    private function getTargetParameterPointValues(Context $context): array
    {
        $routes = $context->getRoutes();
        $targetParameterPointValues = [];

        foreach ($routes as $route) {
            $targetPoint = $route->getTargetPoint();

            if (!$targetPoint instanceof ParameterPoint || '__construct' === $targetPoint->getMethodName()) {
                continue;
            }

            $targetPointValue = $this->getTargetPointValue($context, $route);

            $targetParameterPointValues[$targetPoint->getMethodName()][$targetPoint->getPosition()] = $targetPointValue;
        }

        return $targetParameterPointValues;
    }

    /**
     * Gets target property point values.
     *
     * @param Context $context
     * @return array
     */
    private function getTargetPropertyPointValues(Context $context): array
    {
        $routes = $context->getRoutes();
        $targetPropertyPointValues = [];

        foreach ($routes as $route) {
            $targetPoint = $route->getTargetPoint();

            if (!$targetPoint instanceof PropertyPoint) {
                continue;
            }

            $targetPointValue = $this->getTargetPointValue($context, $route);

            $targetPropertyPointValues[$targetPoint->getName()] = $targetPointValue;
        }

        return $targetPropertyPointValues;
    }

    /**
     * Gets target property points.
     *
     * @param Context $context
     * @return array
     */
    private function getTargetPropertyPoints(Context $context): array
    {
        $routes = $context->getRoutes();
        $targetPropertyPoints = [];

        foreach ($routes as $route) {
            $targetPoint = $route->getTargetPoint();

            if (!$targetPoint instanceof PropertyPoint) {
                continue;
            }

            $targetPropertyPoints[$targetPoint->getName()] = $targetPoint;
        }

        return $targetPropertyPoints;
    }

    /**
     * Gets target point value.
     *
     * @param Context $context
     * @param Route $route
     * @return mixed
     */
    private function getTargetPointValue(Context $context, Route $route)
    {
        $filter = $context->getFilterOnRoute($route);

        if (null !== $filter) {
            try {
                return $filter->getValue($context, $this);
            } catch (NotSupportedContextException $e) {
            }
        }

        return $route->getSourcePoint()->getValue($context->getSource());
    }
}
