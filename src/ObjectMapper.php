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
    public function map(object $source, $target, ?Map $map = null): ?object
    {
        $map = $map ?? $this->mapBuilder->buildMap(true);
        $context = new Context($source, $target, $map);
        $routes = $context->getRoutes();

        if (\count($routes) === 0) {
            return null;
        }

        $targetParameterPointValues = [];
        $targetPropertyPointValues = [];
        $targetPropertyPoints = [];

        foreach ($routes as $route) {
            $targetPoint = $route->getTargetPoint();
            $targetPointValue = $this->getTargetPointValue($context, $route);

            if ($targetPoint instanceof ParameterPoint) {
                $targetParameterPointValues[$targetPoint->getMethodName()][$targetPoint->getPosition()] = $targetPointValue;
            } elseif ($targetPoint instanceof PropertyPoint) {
                $targetPropertyPointValues[$targetPoint->getName()] = $targetPointValue;
                $targetPropertyPoints[$targetPoint->getName()] = $targetPoint;
            }
        }

        $targetClassReflection = $context->getTargetClassReflection();

        if (false === $context->hasInstantiatedTarget()) {
            if (isset($targetParameterPointValues['__construct'])) {
                $target = $targetClassReflection->newInstanceArgs($targetParameterPointValues['__construct']);

                unset($targetParameterPointValues['__construct']);
            } else {
                $target = $targetClassReflection->newInstance();
            }
        }

        foreach ($targetParameterPointValues as $methodName => $methodArguments) {
            $targetClassReflection->getMethod($methodName)->invokeArgs($target, $methodArguments);
        }

        foreach ($targetPropertyPoints as $propertyName => $targetPropertyPoint) {
            $targetPropertyPoint->setValue($target, $targetPropertyPointValues[$propertyName]);
        }

        return $target;
    }

    /**
     * Gets the target point value.
     *
     * @param Context $context
     * @param Route $route
     * @return mixed
     */
    private function getTargetPointValue(Context $context, Route $route)
    {
        $filter = $context->getFilterOnRoute($route);

        if (null !== $filter) {
            return $filter->getValue($route, $context, $this);
        }

        return $route->getSourcePoint()->getValue($context->getSource());
    }
}
