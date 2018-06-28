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
 * @version 1.0.0
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
    public function getMapBuilder() : MapBuilderInterface
    {
        return $this->mapBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function map($sources, $targets, ?MapInterface $map = null)
    {
        if (empty($sources)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s is empty.',
                __METHOD__
            ));
        }

        if (empty($targets)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 3 passed to %s is empty.',
                __METHOD__
            ));
        }

        $sourcesArgumentType = gettype($sources);
        $targetsArgumentType = gettype($targets);

        $sources = is_array($sources) ? $sources : array($sources);
        $targets = is_array($targets) ? $targets : array($targets);

        foreach ($sources as $source) {
            if (!is_object($source)) {
                throw new InvalidArgumentException(sprintf(
                    'Argument 2 passed to %s is invalid. Expects a source to be of type object. %s type given.',
                    __METHOD__,
                    gettype($source)
                ));
            }
        }

        foreach ($targets as $target) {
            if ((!is_string($target)) && (!is_object($target))) {
                throw new InvalidArgumentException(sprintf(
                    'Argument 3 passed to %s is invalid. Expects a target to be of type object or string. %s type given.',
                    __METHOD__,
                    gettype($target)
                ));
            }

            if (is_string($target) && !class_exists($this->getCanonicalClassFqn($target))) {
                throw new InvalidArgumentException(sprintf(
                    'Argument 3 passed to %s is invalid. Target class "%s" does not exist.',
                    __METHOD__,
                    $this->getCanonicalClassFqn($target)
                ));
            }
        }

        $map = $map ?? $this->mapBuilder->buildMap();

        foreach ($sources as $sourceKey => $source) {
            $sourceClassFqn = $this->getCanonicalClassFqn($source);

            foreach ($targets as $targetKey => $target) {
                $targetClassFqn = $this->getCanonicalClassFqn($target);

                $routeCollection = $map->getRouteCollection($sourceClassFqn, $targetClassFqn);

                if (!$routeCollection->hasRoutes()) {
                    continue;
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

                $targetClassReflection = new \ReflectionClass($this->getCanonicalClassFqn($target));

                if (is_string($target)) {
                    if (isset($targetParameterPointValues['__construct'])) {
                        $targets[$targetKey] = $targetClassReflection->newInstanceArgs($targetParameterPointValues['__construct']);

                        continue;

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

                $targets[$targetKey] = $target;
            }
        }

        return ('array' === $targetsArgumentType) ? $targets : array_values($targets)[0];
    }

    /**
     * Gets the canonical Fully Qualified Name of the class.
     *
     * @param  string|object $canonicalized
     * @return string
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    protected function getCanonicalClassFqn($canonicalized) : string
    {
        if (is_object($canonicalized)) {
            $class = get_class($canonicalized);

        } elseif (is_string($canonicalized)) {
            $class = $canonicalized;

        } else{
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s is invalid. Expects the canonicalized to be of type object or string. %s type given.',
                __METHOD__,
                gettype($canonicalized)
            ));
        }

        // Checks for Doctrine2 proxies...
        if (false !== strpos($class, "Proxies\\__CG__\\")) {
            $class = mb_substr($class, strlen("Proxies\\__CG__\\"), strlen($class));
        }

        return $class;
    }
}

