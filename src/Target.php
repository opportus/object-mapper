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

use Exception;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\MethodParameterDynamicTargetPoint;
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use Opportus\ObjectMapper\Point\PropertyDynamicTargetPoint;
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Point\StaticTargetPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use ReflectionClass;
use ReflectionException;

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
     * @var ReflectionClass $reflection
     */
    private $reflection;

    /**
     * @var null|object $instance
     */
    private $instance;

    /**
     * @var array $pointValues
     */
    private $pointValues;

    /**
     * @var bool $isOperated
     */
    private $isOperated;

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
            $this->reflection = new ReflectionClass($target);
        } catch (ReflectionException $exception) {
            $message = \sprintf(
                '%s is not a target. %s',
                $target,
                $exception->getMessage()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        $this->instance = \is_object($target) ? $target : null;
        $this->isOperated = false;
        $this->pointValues = [
            'static_properties' => [],
            'static_method_parameters' => [],
            'dynamic_properties' => [],
            'dynamic_method_parameters' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFqn(): string
    {
        return $this->reflection->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this->reflection->getName());
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
    public function hasStaticPoint(StaticTargetPointInterface $point): bool
    {
        return $this->getFqn() === $point->getTargetFqn();
    }

    /**
     * {@inheritdoc}
     */
    public function setPointValue(TargetPointInterface $point, $pointValue)
    {
        if ($point instanceof StaticTargetPointInterface &&
            false === $this->hasStaticPoint($point)
        ) {
            $message = \sprintf(
                '%s is not a static target point of %s.',
                $point->getFqn(),
                $this->reflection->getName()
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
    public function operateInstance()
    {
        if ($this->isOperated) {
            $message = \sprintf(
                'Cannot operate already operated %s target.',
                $this->fqn
            );

            throw new InvalidOperationException(
                __METHOD__,
                $message
            );
        }

        try {
            $this->operateInstanceSafely(
                $isSafeOperation = (false === $this->isInstantiated())
            );
        } catch (Exception $exception) {
            throw new InvalidOperationException(
                __METHOD__,
                $exception->getMessage(),
                0,
                $exception
            );
        } finally {
            $this->isOperated = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isInstantiated(): bool
    {
        return (bool)$this->instance;
    }

    /**
     * {@inheritdoc}
     */
    public function isOperated(): bool
    {
        return $this->isOperated;
    }

    /**
     * Operates instance safely.
     *
     * @param boolean $isSafeOperation
     * @param null|object $instance
     */
    private function operateInstanceSafely($isSafeOperation = false, ?object $instance = null)
    {
        if ($isSafeOperation) {
            $instance = $this->instance;
        }

        if (null === $instance) {
            if (isset($this->pointValues['static_method_parameters']['__construct'])) {
                $instance = $this->reflection->newInstanceArgs(
                    $this->pointValues['static_method_parameters']['__construct']
                );
            } else {
                $instance = $this->reflection->newInstance();
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

            $this->reflection->getMethod($methodName)->invokeArgs(
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
            $this->reflection->getProperty($propertyName)->setValue(
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
            $this->operateInstanceSafely(true, $instance);
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
            $this->reflection->getMethod($point->getMethodName())
                ->getParameters() as
            $parameter
        ) {
            if ($parameter->getName() === $point->getName()) {
                return $parameter->getPosition();
            }
        }
    }

    /**
     * Checks whether target point values have been set.
     *
     * @return bool
     */
    private function hasPointValues(): bool
    {
        return
            $this->pointValues['static_properties'] ||
            $this->pointValues['static_method_parameters'] ||
            $this->pointValues['dynamic_properties'] ||
            $this->pointValues['dynamic_method_parameters'];
    }
}
