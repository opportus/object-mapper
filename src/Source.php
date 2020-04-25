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
use Opportus\ObjectMapper\Point\MethodObjectPoint;
use Opportus\ObjectMapper\Point\ObjectPoint;
use Opportus\ObjectMapper\Point\PropertyObjectPoint;
use ReflectionClass;

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
     * @var ReflectionClass $reflection
     */
    private $reflection;

    /**
     * @var object $instance
     */
    private $instance;

    /**
     * Constructs the source.
     *
     * @param object $source
     */
    public function __construct(object $source)
    {
        $this->reflection = new ReflectionClass($source);
        $this->instance = $source;
    }

    /**
     * Gets the source reflection.
     *
     * @return ReflectionClass
     */
    public function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this->reflection->getName());
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
     * Checks whether the passed argument can be a source point.
     *
     * @param ObjectPoint $point
     * @return bool
     */
    public static function isPoint(ObjectPoint $point)
    {
        return
            $point instanceof PropertyObjectPoint ||
            $point instanceof MethodObjectPoint;
    }

    /**
     * Checks whether the source has the passed point.
     *
     * @param ObjectPoint $point
     * @return bool
     */
    public function hasPoint(ObjectPoint $point): bool
    {
        return
            self::isPoint($point) &&
            $this->reflection->getName() === $point->getClassFqn();
    }

    /**
     * Gets the value of the passed source point.
     *
     * @param ObjectPoint $point
     * @return mixed
     * @throws InvalidArgumentException
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function getPointValue(ObjectPoint $point)
    {
        if (false === $this->hasPoint($point)) {
            $message = \sprintf(
                '%s is not a property of %s.',
                $point->getFqn(),
                $this->reflection->getName()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        if ($point instanceof PropertyObjectPoint) {
            return $this->reflection->getProperty($point->getName())
                    ->getValue($this->instance);
        } elseif ($point instanceof MethodObjectPoint) {
            return $this->reflection->getMethod($point->getName())
                    ->invoke($this->instance);
        }
    }
}
