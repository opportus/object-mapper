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
     * @var null|object $instance
     */
    private $instance;

    /**
     * @var string $classFqn
     */
    private $classFqn;

    /**
     * @var ReflectionClass $classReflection
     */
    private $classReflection;

    /**
     * @var array $pointValues
     */
    private $pointValues = [
        'properties' => [],
        'methods'    => [],
    ];

    /**
     * Constructs the target.
     *
     * @param object|string $target
     * @throws InvalidArgumentException
     */
    public function __construct($target)
    {
        if (false === \is_object($target) && false === \is_string($target)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "target" passed to "%s" is invalid. Expecting an argument of type "object" or "string", got an argument of type "%s".',
                __METHOD__,
                \gettype($target)
            ));
        }

        try {
            $this->classReflection = new ReflectionClass($target);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "target" passed to "%s" is invalid. "%s" is not a target. %s.',
                __METHOD__,
                $target,
                $exception->getMessage()
            ));
        }

        $this->instance = \is_object($target) ? $target : null;
        $this->classFqn = $this->classReflection->getName();
    }

    /**
     * Checks whether the passed argument can be a target point.
     *
     * @param AbstractPoint $point
     * @return bool
     */
    public static function isValidPoint(AbstractPoint $point): bool
    {
        return $point instanceof PropertyPoint ||
               $point instanceof ParameterPoint;
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
            return $this->operateInstance($this->instance, $this->pointValues);
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
     * Gets the target class Fully Qualified Name.
     *
     * @return string
     */
    public function getClassFqn(): string
    {
        return $this->classFqn;
    }

    /**
     * Gets the target class reflection.
     *
     * @return ReflectionClass
     * @throws InvalidOperationException
     */
    public function getClassReflection(): ReflectionClass
    {
        try {
            return new ReflectionClass($this->classFqn);
        } catch (ReflectionException $exception) {
            throw new InvalidOperationException(\sprintf(
                'Invalid "%s" operation. %s',
                __METHOD__,
                $exception->getMessage()
            ));
        }
    }

    /**
     * Checks whether the target has the passed point.
     *
     * @param AbstractPoint $point
     * @return bool
     */
    public function hasPoint(AbstractPoint $point): bool
    {
        return $this->classFqn === $point->getClassFqn();
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
            throw new InvalidArgumentException(
                \sprintf(
                    'Argument "point" passed to "%s" is invalid. "%s" is not a property of "%s".',
                    __METHOD__,
                    $point->getFqn(),
                    $this->classFqn
                )
            );
        }

        if (false === self::isValidPoint($point)) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Argument "point" passed to "%s" is invalid. "%s" is not a valid source point.',
                    __METHOD__,
                    \get_class($point)
                )
            );
        }

        if ($point instanceof PropertyPoint) {
            $this->pointValues['properties'][$point->getName()] = $pointValue;
        } elseif ($point instanceof ParameterPoint) {
            $this->pointValues['parameters']
                [$point->getMethodName()][$point->getPosition()] = $pointValue;
        } else {
            throw new InvalidArgumentException(\sprintf(
                'Argument "point" passed to "%s" is invalid. "%s" is not a valid target point.',
                __METHOD__,
                \get_class($point)
            ));
        }
    }

    /**
     * Creates/updates the target instance.
     *
     * @param null|object $instance
     * @param array $pointValues
     * @return object
     * @throws InvalidOperationException
     */
    private function operateInstance(
        ?object $instance,
        array $pointValues
    ): object {
        if (null === $instance) {
            if (isset($pointValues['parameters']['__construct'])) {
                $instance = $this->classReflection->newInstanceArgs(
                    $pointValues['parameters']['__construct']
                );
            } else {
                $instance = $this->classReflection->newInstance();
            }
        }

        try {
            foreach (
                $pointValues['parameters'] as
                $methodName =>
                $methodArguments
            ) {
                if ('__construct' === $methodName) {
                    continue;
                }

                $this->classReflection->getMethod($methodName)->invokeArgs(
                    $instance,
                    $methodArguments
                );
            }

            foreach (
                $pointValues['properties'] as
                $propertyName =>
                $propertyValue
            ) {
                $this->classReflection->getProperty($propertyName)->setValue(
                    $instance,
                    $propertyValue
                );
            }
        } catch (ReflectionException $exception) {
            throw new InvalidOperationException(\sprintf(
                'Invalid "%s" operation. %s',
                __METHOD__,
                $exception->getMessage()
            ));
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
