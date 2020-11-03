<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Error;
use Exception;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\MethodParameterDynamicTargetPoint;
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use Opportus\ObjectMapper\Point\PropertyDynamicTargetPoint;
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

/**
 * The target.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class Target implements TargetInterface
{
    /**
     * @var ReflectionClass $classReflection
     */
    private $classReflection;

    /**
     * @var null|ReflectionObject $objectReflection
     */
    private $objectReflection;

    /**
     * @var null|object $instance
     */
    private $instance;

    /**
     * @var array $pointValues
     */
    private $pointValues;

    /**
     * Constructs the target.
     *
     * @param object|string $target
     * @throws InvalidArgumentException
     */
    public function __construct($target)
    {
        if (false === \is_object($target) && false === \is_string($target)) {
            $message = \sprintf(
                'The argument must be of type object or string, got an argument of type %s.',
                \gettype($target)
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        try {
            $this->classReflection = new ReflectionClass($target);
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a target. %s',
                $target,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        if (\is_object($target)) {
            $this->objectReflection = new ReflectionObject($target);
            $this->instance = $target;
        }

        $this->pointValues = $this->initializePointValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getFqn(): string
    {
        return $this->classReflection->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getClassReflection(): ReflectionClass
    {
        return new ReflectionClass($this->classReflection->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectReflection(): ?ReflectionObject
    {
        if (null !== $this->instance) {
            return new ReflectionObject($this->instance);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(): ?object
    {
        return $this->instance;
    }

    /**
     * {@inheritdoc}
     */
    public function setPointValue(TargetPointInterface $point, $pointValue)
    {
        if ($this->getFqn() !== $point->getTargetFqn()) {
            $message = \sprintf(
                '%s is not a point of target %s.',
                $point->getFqn(),
                $this->getFqn()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        if ($point instanceof PropertyStaticTargetPoint) {
            $this->pointValues['static_properties'][$point->getName()] = $pointValue;
        } elseif ($point instanceof MethodParameterStaticTargetPoint) {
            $this->pointValues['static_method_parameters'][$point->getMethodName()]
                [$this->getMethodParameterStaticPointPosition($point)] = $pointValue;
        } elseif ($point instanceof PropertyDynamicTargetPoint) {
            $this->pointValues['dynamic_properties']
                [$point->getName()] = $pointValue;
        } elseif ($point instanceof MethodParameterDynamicTargetPoint) {
            $this->pointValues['dynamic_method_parameters']
                [$point->getMethodName()][] = $pointValue;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function operate()
    {
        try {
            $this->operateSafely(null === $this->getInstance());
        } catch (Error|Exception $exception) {
            throw new InvalidOperationException(
                __METHOD__,
                $exception->getMessage(),
                0,
                $exception
            );
        } finally {
            $this->pointValues = $this->initializePointValues();
        }

        if (null === $this->getObjectReflection()) {
            $this->objectReflection = new ReflectionObject(
                $this->getInstance()
            );
        }
    }

    /**
     * Operates instance safely.
     *
     * @param boolean $isSafeOperation
     * @param null|object $instance
     */
    private function operateSafely($isSafeOperation = false, ?object $instance = null)
    {
        if ($isSafeOperation) {
            $instance = $this->instance;
        }

        if (null === $instance) {
            if (isset($this->pointValues['static_method_parameters']['__construct'])) {
                $instance = $this->classReflection->newInstanceArgs(
                    $this->pointValues['static_method_parameters']['__construct']
                );
            } else {
                $instance = $this->classReflection->newInstance();
            }
        } elseif (false === $isSafeOperation) {
            $instance = clone $instance;
        }

        foreach (
            $this->pointValues['static_method_parameters'] as
            $methodName =>
            $methodArguments
        ) {
            if ('__construct' === $methodName) {
                continue;
            }
            $methodReflection = $this->classReflection->getMethod($methodName);

            $methodReflection->setAccessible(true);

            $methodReflection->invokeArgs(
                $instance,
                $methodArguments
            );
        }

        foreach (
            $this->pointValues['dynamic_method_parameters'] as
            $methodName =>
            $methodArguments
        ) {
            $instance->{$methodName}(...$methodArguments);
        }

        foreach (
            $this->pointValues['static_properties'] as
            $propertyName =>
            $propertyValue
        ) {
            $propertyReflection = $this->classReflection->getProperty($propertyName);

            $propertyReflection->setAccessible(true);
            
            $propertyReflection->setValue(
                $instance,
                $propertyValue
            );
        }

        foreach (
            $this->pointValues['dynamic_properties'] as
            $propertyName =>
            $propertyValue
        ) {
            $instance->{$propertyName} = $propertyValue;
        }

        if ($isSafeOperation) {
            $this->instance = $instance;
        } else {
            $this->operateSafely(true, $instance);
        }
    }

    /**
     * Gets the method parameter static point position.
     *
     * @param MethodParameterStaticTargetPoint $point
     * @return int
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    private function getMethodParameterStaticPointPosition(MethodParameterStaticTargetPoint $point): int
    {
        foreach (
            $this->classReflection->getMethod($point->getMethodName())
                ->getParameters() as
            $parameter
        ) {
            if ($parameter->getName() === $point->getName()) {
                return $parameter->getPosition();
            }
        }
    }

    /**
     * Initializes point values.
     *
     * @return array
     */
    private function initializePointValues(): array
    {
        return [
            'static_properties' => [],
            'static_method_parameters' => [],
            'dynamic_properties' => [],
            'dynamic_method_parameters' => [],
        ];
    }
}
