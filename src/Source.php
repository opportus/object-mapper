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
use Opportus\ObjectMapper\Point\MethodDynamicSourcePoint;
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyDynamicSourcePoint;
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use ReflectionClass;
use ReflectionObject;

/**
 * The source.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class Source implements SourceInterface
{
    /**
     * @var ReflectionClass $classReflection
     */
    private $classReflection;

    /**
     * @var ReflectionObject $objectReflection
     */
    private $objectReflection;

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
        $this->classReflection = new ReflectionClass($source);
        $this->objectReflection = new ReflectionObject($source);
        $this->instance = $source;
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
    public function getObjectReflection(): ReflectionObject
    {
        return new ReflectionObject($this->instance);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(): object
    {
        return $this->instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getPointValue(SourcePointInterface $point)
    {
        if ($this->getFqn() !== $point->getSourceFqn()) {
            $message = \sprintf(
                '%s is not a point of source %s.',
                $point->getFqn(),
                $this->getFqn()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        try {
            if ($point instanceof PropertyStaticSourcePoint) {
                $propertyReflection = $this->classReflection
                    ->getProperty($point->getName());

                $propertyReflection->setAccessible(true);

                return $propertyReflection->getValue($this->instance);
            } elseif ($point instanceof MethodStaticSourcePoint) {
                $methodReflection = $this->classReflection
                    ->getMethod($point->getName());

                $methodReflection->setAccessible(true);

                return $methodReflection->invoke($this->instance);
            } elseif ($point instanceof PropertyDynamicSourcePoint) {
                return $this->instance->{$point->getName()};
            } elseif ($point instanceof MethodDynamicSourcePoint) {
                return $this->instance->{$point->getName()}();
            }
        } catch (Exception $exception) {
            throw new InvalidOperationException(
                __METHOD__,
                $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}
