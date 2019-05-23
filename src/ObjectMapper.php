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
class ObjectMapper implements ObjectMapperInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\MapBuilderInterface $mapBuilder
     */
    protected $mapBuilder;

    /**
     * Constructs the mapper.
     *
     * @param Opportus\ObjectMapper\Map\MapBuilderInterface $mapBuilder
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
        $routeCollection = $map->getRouteCollection($this->getCanonicalFqcn($source), $this->getCanonicalFqcn($target));

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

        $targetClassReflection = new \ReflectionClass($this->getCanonicalFqcn($target));

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

    /**
     * Gets the canonical FQCN.
     *
     * @param  string|object $object
     * @return string
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    private function getCanonicalFqcn($object): string
    {
        if (\is_object($object)) {
            $class = \get_class($object);

        } elseif (\is_string($object)) {
            $class = $object;

        } else{
            throw new InvalidArgumentException(\sprintf(
                'Argument "object" passed to "%s" is invalid. Expects an argument of type object or string, got an argument of type "%s".',
                __METHOD__,
                \gettype($object)
            ));
        }

        // Checks for Doctrine2 proxies...
        if (false !== \strpos($class, "Proxies\\__CG__\\")) {
            $class = \mb_substr($class, \strlen("Proxies\\__CG__\\"), \strlen($class));
        }

        return $class;
    }
}
