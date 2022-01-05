<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Error;
use Exception;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidObjectOperationException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\MethodParameterDynamicTargetPoint;
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use Opportus\ObjectMapper\Point\PropertyDynamicTargetPoint;
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use Throwable;

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

            throw new InvalidArgumentException(1, $message);
        }

        try {
            $this->classReflection = new ReflectionClass($target);
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a target. %s',
                $target,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, $message);
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

            throw new InvalidArgumentException(1, $message);
        }

        if ($point instanceof PropertyStaticTargetPoint) {
            $this->pointValues['static_properties'][$point->getName()] = $pointValue;
        } elseif ($point instanceof MethodParameterStaticTargetPoint) {
            $this->pointValues['static_method_parameters'][$point->getMethodName()]
                [$this->getMethodParameterStaticPointPosition($point)] = $pointValue;
            \ksort($this->pointValues['static_method_parameters'][$point->getMethodName()]);
        } elseif ($point instanceof PropertyDynamicTargetPoint) {
            $this->pointValues['dynamic_properties']
                [$point->getName()] = $pointValue;
        } elseif ($point instanceof MethodParameterDynamicTargetPoint) {
            $this->pointValues['dynamic_method_parameters']
                [$point->getMethodName()][] = $pointValue;
        } else {
            $message = \sprintf(
                'Target point type %s not supported.',
                \get_class($point)
            );

            throw new InvalidArgumentException(1, $message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function operate()
    {
        $instance = $this->getInstance();

        try {
            if (null !== $instance) {
                $this->operateInstance(clone $instance);
            }

            $instance = $this->operateInstance($instance);
        } catch (Throwable $exception) {
            throw $exception;
        } finally {
            $this->pointValues = $this->initializePointValues();
        }

        $this->update($instance);
    }

    /**
     * Operates an instance.
     *
     * @param null|object $instance
     * @return object
     * @throws InvalidObjectOperationException
     * @throws InvalidOperationException
     */
    private function operateInstance(?object $instance = null): object
    {
        if (null === $instance) {
            if (isset($this->pointValues['static_method_parameters']['__construct'])) {
                try {
                    $instance = $this->classReflection->newInstanceArgs(
                        $this->pointValues['static_method_parameters']['__construct']
                    );
                } catch (ReflectionException $exception) {
                    throw new InvalidOperationException(
                        $exception->getMessage(),
                        0,
                        $exception
                    );
                } catch (Exception $exception) {
                    throw new InvalidObjectOperationException(
                        $exception->getMessage(),
                        0,
                        $exception
                    );
                }
            } else {
                try {
                    $instance = $this->classReflection->newInstance();
                } catch (ReflectionException $exception) {
                    throw new InvalidOperationException(
                        $exception->getMessage(),
                        0,
                        $exception
                    );
                }
            }
        }

        foreach (
            $this->pointValues['static_method_parameters'] as
            $methodName =>
            $methodArguments
        ) {
            if ('__construct' === $methodName) {
                continue;
            }

            try {
                $methodReflection = $this->classReflection->getMethod($methodName);
            } catch (ReflectionException $exception) {
                throw new InvalidOperationException(
                    $exception->getMessage(),
                    0,
                    $exception
                );
            }

            $methodReflection->setAccessible(true);

            try {
                $methodReflection->invokeArgs($instance, $methodArguments);
            } catch (ReflectionException $exception) {
                throw new InvalidOperationException(
                    $exception->getMessage(),
                    0,
                    $exception
                );
            } catch (Exception $exception) {
                throw new InvalidObjectOperationException(
                    $exception->getMessage(),
                    0,
                    $exception
                );
            }
        }

        foreach (
            $this->pointValues['dynamic_method_parameters'] as
            $methodName =>
            $methodArguments
        ) {
            try {
                $instance->{$methodName}(...$methodArguments);
            } catch (Error $exception) {
                throw new InvalidOperationException(
                    $exception->getMessage(),
                    0,
                    $exception
                );
            } catch (Exception $exception) {
                throw new InvalidObjectOperationException(
                    $exception->getMessage(),
                    0,
                    $exception
                );
            }
        }

        foreach (
            $this->pointValues['static_properties'] as
            $propertyName =>
            $propertyValue
        ) {
            try {
                $propertyReflection = $this->classReflection->getProperty($propertyName);
            } catch (ReflectionException $exception) {
                throw new InvalidOperationException(
                    $exception->getMessage(),
                    0,
                    $exception
                );
            }

            $propertyReflection->setAccessible(true);

            $propertyReflection->setValue($instance, $propertyValue);
        }

        foreach (
            $this->pointValues['dynamic_properties'] as
            $propertyName =>
            $propertyValue
        ) {
            try {
                $instance->{$propertyName} = $propertyValue;
            } catch (Error $exception) {
                throw new InvalidOperationException(
                    $exception->getMessage(),
                    0,
                    $exception
                );
            }
        }

        return $instance;
    }

    /**
     * Updates.
     *
     * @param  object $instance
     * @return void
     */
    private function update(object $instance)
    {
        $this->instance = $instance;

        if (null === $this->objectReflection) {
            $this->objectReflection = new ReflectionObject($instance);
        }
    }

    /**
     * Gets the method parameter static point position.
     *
     * @param MethodParameterStaticTargetPoint $point
     * @return int
     */
    private function getMethodParameterStaticPointPosition(
        MethodParameterStaticTargetPoint $point
    ): int {
        foreach (
            $this->classReflection->getMethod($point->getMethodName())
                ->getParameters() as
            $parameter
        ) {
            if ($parameter->getName() === $point->getName()) {
                $position = $parameter->getPosition();
                break;
            }
        }

        return $position;
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
