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
use Opportus\ObjectMapper\Map\Route\Point\AbstractPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
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
     * @var ReflectionClass $reflector
     */
    private $reflector;

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
            $this->reflector = new ReflectionClass($target);
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
            'properties' => [],
            'methods' => [],
        ];
    }

    /**
     * Gets the target reflector.
     *
     * @return ReflectionClass
     */
    public function getReflector(): ReflectionClass
    {
        return new ReflectionClass($this->reflector->getName());
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
            } catch (Exception $exception) {
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
     * Checks whether the passed point can be a target point.
     *
     * @param AbstractPoint $point
     * @return bool
     */
    public static function isValidPoint(AbstractPoint $point): bool
    {
        return
            $point instanceof PropertyPoint ||
            $point instanceof ParameterPoint;
    }

    /**
     * Checks whether the target has the passed point.
     *
     * @param AbstractPoint $point
     * @return bool
     */
    public function hasPoint(AbstractPoint $point): bool
    {
        if (self::isValidPoint($point)) {
            if ($point instanceof PropertyPoint) {
                return
                    $this->reflector->getName() === $point->getClassFqn() &&
                    $this->reflector->hasProperty($point->getName());
            } elseif ($point instanceof ParameterPoint) {
                if (
                    $this->reflector->getName() === $point->getClassFqn() &&
                    $this->reflector->hasMethod($point->getMethodName())
                ) {
                    foreach (
                        $this->reflector->getMethod($point->getMethodName())
                            ->getParameters() as
                        $parameter
                    ) {
                        if ($parameter->getName() === $point->getName()) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Sets the value of the passed target point.
     *
     * @param AbstractPoint $point
     * @param $pointValue
     * @throws InvalidArgumentException
     */
    public function setPointValue(AbstractPoint $point, $pointValue)
    {
        if (false === $this->hasPoint($point)) {
            $message = \sprintf(
                '%s is not a property of %s.',
                $point->getFqn(),
                $this->reflector->getName()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        if ($point instanceof PropertyPoint) {
            $this->pointValues['properties'][$point->getName()] = $pointValue;
        } elseif ($point instanceof ParameterPoint) {
            $this->pointValues['parameters'][$point->getMethodName()]
                [$this->getParameterPointPosition($point)] = $pointValue;
        }
    }

    /**
     * Gets the parameter point position.
     *
     * @param ParameterPoint $point
     * @return int
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    private function getParameterPointPosition(ParameterPoint $point): int
    {
        foreach (
            $this->reflector->getMethod($point->getMethodName())
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
     */
    private function operateInstance(
        ?object $instance,
        array $pointValues
    ): object {
        if (null === $instance) {
            if (isset($pointValues['parameters']['__construct'])) {
                $instance = $this->reflector->newInstanceArgs(
                    $pointValues['parameters']['__construct']
                );
            } else {
                $instance = $this->reflector->newInstance();
            }
        }

        foreach (
            $pointValues['parameters'] as
            $methodName =>
            $methodArguments
        ) {
            if ('__construct' === $methodName) {
                continue;
            }

            $this->reflector->getMethod($methodName)->invokeArgs(
                $instance,
                $methodArguments
            );
        }

        foreach (
            $pointValues['properties'] as
            $propertyName =>
            $propertyValue
        ) {
            $this->reflector->getProperty($propertyName)->setValue(
                $instance,
                $propertyValue
            );
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
        return $this->pointValues['properties'] ||
            $this->pointValues['parameters'];
    }
}
