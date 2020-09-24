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
final class Target
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
        $this->pointValues = [
            'static_properties' => [],
            'static_method_parameters' => [],
            'dynamic_properties' => [],
            'dynamic_method_parameters' => [],
        ];
    }

    /**
     * Gets the target Fully Qualified Name.
     *
     * @return string
     */
    public function getFqn(): string
    {
        return $this->reflection->getName();
    }

    /**
     * Gets the target reflection.
     *
     * @return ReflectionClass
     */
    public function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this->reflection->getName());
    }

    /**
     * Gets the target instance.
     *
     * @return null|object
     * @throws InvalidOperationException
     */
    public function getInstance(): ?object
    {
        if ($this->hasPointValues()) {
            try {
                return $this->operateInstance(
                    $this->instance,
                    $this->pointValues
                );
            } catch (ReflectionException $exception) {
                throw new InvalidOperationException(
                    __METHOD__,
                    $exception->getMessage()
                );
            }
        }

        return $this->instance;
    }

    /**
     * Checks whether the target is instantiated.
     *
     * @return bool
     */
    public function isInstantiated(): bool
    {
        return (bool)$this->instance;
    }

    /**
     * Checks whether the target has the passed static point.
     *
     * @param StaticTargetPointInterface $point
     * @return bool
     */
    public function hasStaticPoint(StaticTargetPointInterface $point): bool
    {
        return $this->reflection->getName() === $point->getTargetFqn();
    }

    /**
     * Sets the value of the passed target point.
     *
     * @param TargetPointInterface $point
     * @param mixed $pointValue
     * @throws InvalidArgumentException
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
     * Creates/updates the target instance.
     *
     * @param null|object $instance
     * @param array $pointValues
     * @return object
     * @throws ReflectionException
     */
    private function operateInstance(
        ?object $instance,
        array $pointValues
    ): object {
        if (null === $instance) {
            if (isset($pointValues['static_method_parameters']['__construct'])) {
                $instance = $this->reflection->newInstanceArgs(
                    $pointValues['static_method_parameters']['__construct']
                );
            } else {
                $instance = $this->reflection->newInstance();
            }
        }

        foreach (
            $pointValues['static_method_parameters'] as
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
            $pointValues['dynamic_method_parameters'] as
            $methodName =>
            $methodArguments
        ) {
            if ('__construct' === $methodName) {
                continue;
            }

            $instance->{$methodName}(...$methodArguments);
        }

        foreach (
            $pointValues['static_properties'] as
            $propertyName =>
            $propertyValue
        ) {
            $this->reflection->getProperty($propertyName)->setValue(
                $instance,
                $propertyValue
            );
        }

        foreach (
            $pointValues['dynamic_properties'] as
            $propertyName =>
            $propertyValue
        ) {
            $instance->{$propertyName} = $propertyValue;
        }

        return $instance;
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
