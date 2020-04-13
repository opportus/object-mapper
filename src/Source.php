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
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use ReflectionClass;
use ReflectionException;

/**
 * The source.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class Source
{
    /**
     * @var object $instance
     */
    private $instance;

    /**
     * @var string
     */
    private $classFqn;

    /**
     * @var ReflectionClass $classReflection
     */
    private $classReflection;

    /**
     * Constructs the source.
     *
     * @param object $source
     * @throws InvalidArgumentException
     */
    public function __construct(object $source)
    {
        try {
            $this->classReflection = new ReflectionClass($source);
        } catch (ReflectionException $exception) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "source" passed to "%s" is invalid. "%s" is not a source. %s.',
                __METHOD__,
                $source,
                $exception->getMessage()
            ));
        }

        $this->instance = $source;
        $this->classFqn = $this->classReflection->getName();
    }

    /**
     * Checks whether the passed argument can be a source point.
     *
     * @param AbstractPoint $point
     * @return bool
     */
    public static function isValidPoint(AbstractPoint $point)
    {
        return $point instanceof PropertyPoint ||
               $point instanceof MethodPoint;
    }

    /**
     * Gets the source instance.
     *
     * @return object
     */
    public function getInstance(): object
    {
        return $this->instance;
    }

    /**
     * Gets the source class Fully Qualified Name.
     *
     * @return string
     */
    public function getClassFqn(): string
    {
        return $this->classFqn;
    }

    /**
     * Gets the source class reflection.
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
     * Checks whether the source has the passed point.
     *
     * @param AbstractPoint $point
     * @return bool
     */
    public function hasPoint(AbstractPoint $point): bool
    {
        return $this->classFqn === $point->getClassFqn();
    }

    /**
     * Gets the value of the passed source point.
     *
     * @param AbstractPoint $point
     * @return mixed
     * @throws InvalidArgumentException
     * @throws InvalidOperationException
     */
    public function getPointValue(AbstractPoint $point)
    {
        if (false === $this->hasPoint($point)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "point" passed to "%s" is invalid. "%s" is not a property of "%s".',
                __METHOD__,
                $point->getFqn(),
                $this->classFqn
            ));
        }

        if (false === self::isValidPoint($point)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "point" passed to "%s" is invalid. "%s" is not a valid source point.',
                __METHOD__,
                \get_class($point)
            ));
        }

        try {
            if ($point instanceof PropertyPoint) {
                return $this->classReflection->getProperty($point->getName())
                    ->getValue($this->instance);
            } elseif ($point instanceof MethodPoint) {
                return $this->classReflection->getMethod($point->getName())
                    ->invoke($this->instance);
            }
        } catch (ReflectionException $exception) {
            throw new InvalidOperationException(\sprintf(
                'Invalid "%s" operation. %s',
                __METHOD__,
                $exception->getMessage()
            ));
        }

        throw new InvalidArgumentException(\sprintf(
            'Argument "point" passed to "%s" is invalid. "%s" is not a valid source point.',
            __METHOD__,
            \get_class($point)
        ));
    }
}
