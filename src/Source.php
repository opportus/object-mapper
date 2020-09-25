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
use Opportus\ObjectMapper\Point\StaticSourcePointInterface;
use ReflectionClass;

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
    public function getInstance(): object
    {
        return $this->instance;
    }

    /**
     * {@inheritdoc}
     */
    public function hasStaticPoint(StaticSourcePointInterface $point): bool
    {
        return $this->getFqn() === $point->getSourceFqn();
    }

    /**
     * {@inheritdoc}
     */
    public function getPointValue(SourcePointInterface $point)
    {
        if ($point instanceof StaticSourcePointInterface &&
            false === $this->hasStaticPoint($point)
        ) {
            $message = \sprintf(
                '%s is not a static source point of %s.',
                $point->getFqn(),
                $this->reflection->getName()
            );

            throw new InvalidArgumentException(1, __METHOD__, $message);
        }

        if ($point instanceof PropertyStaticSourcePoint) {
            return $this->reflection->getProperty($point->getName())
                    ->getValue($this->instance);
        } elseif ($point instanceof MethodStaticSourcePoint) {
            return $this->reflection->getMethod($point->getName())
                    ->invoke($this->instance);
        } elseif ($point instanceof PropertyDynamicSourcePoint) {
            try {
                return $this->instance->{$point->getName()};
            } catch (Exception $exception) {
                throw new InvalidOperationException(
                    __METHOD__,
                    \sprintf(
                        'Cannot call property dynamic source point: %s.',
                        $point->getFqn()
                    )
                );
            }
        } elseif ($point instanceof MethodDynamicSourcePoint) {
            try {
                return $this->instance->{$point->getName()}();
            } catch (Exception $exception) {
                throw new InvalidOperationException(
                    __METHOD__,
                    \sprintf(
                        'Cannot call method dynamic source point: %s.',
                        $point->getFqn()
                    )
                );
            }
        }
    }
}
