<?php

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;

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
     * @var Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     */
    private $classCanonicalizer;

    /**
     * @var Opportus\ObjectMapper\Map\MapBuilderInterface $mapBuilder
     */
    private $mapBuilder;

    /**
     * Constructs the object mapper.
     *
     * @param Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     * @param Opportus\ObjectMapper\Map\MapBuilderInterface $mapBuilder
     */
    public function __construct(ClassCanonicalizerInterface $classCanonicalizer, MapBuilderInterface $mapBuilder)
    {
        $this->classCanonicalizer = $classCanonicalizer;
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
    public function map(object $source, $target, ?MapInterface $map = null): ?object
    {
        if (!\is_string($target) && !\is_object($target)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "target" passed to "%s" is invalid. Expects an argument of type object or string, got an argument of type "%s".',
                __METHOD__,
                \gettype($target)
            ));
        }

        $map = $map ?? $this->mapBuilder->buildMap();
        $routeCollection = $map->getRouteCollection($source, $target);

        if (false === $routeCollection->hasRoutes()) {
            return null;
        }

        foreach ($routeCollection as $route) {
            $sourcePoint = $route->getSourcePoint();
            $targetPoint = $route->getTargetPoint();

            $targetPointValue = $sourcePoint->getValue($source);

            if ($targetPoint instanceof ParameterPoint) {
                $targetParameterPointValues[$targetPoint->getMethodName()][$targetPoint->getPosition()] = $targetPointValue;

            } elseif ($targetPoint instanceof PropertyPoint) {
                $targetPropertyPointValues[$targetPoint->getName()] = $targetPointValue;
                $targetPropertyPoints[$targetPoint->getName()] = $targetPoint;
            }
        }

        $targetClassReflection = new \ReflectionClass($this->classCanonicalizer->getCanonicalFqcn($target));

        if (\is_string($target)) {
            if (isset($targetParameterPointValues['__construct'])) {
                $target = $targetClassReflection->newInstanceArgs($targetParameterPointValues['__construct']);

            } else {
                $target = $targetClassReflection->newInstance();
            }
        }

        if (isset($targetParameterPointValues)) {
            unset($targetParameterPointValues['__construct']);

            foreach ($targetParameterPointValues as $methodName => $methodArguments) {
                $targetClassReflection->getMethod($methodName)->invokeArgs($target, $methodArguments);
            }
        }

        if (isset($targetPropertyPoints)) {
            foreach ($targetPropertyPoints as $propertyName => $targetPropertyPoint) {
                $targetPropertyPoint->setValue($target, $targetPropertyPointValues[$propertyName]);
            }
        }

        return $target;
    }
}
