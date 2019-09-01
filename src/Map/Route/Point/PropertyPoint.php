<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\Exception\InvalidPropertyPointException;
use Opportus\ObjectMapper\Exception\InvalidPropertyPointSyntaxException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use ReflectionException;
use ReflectionProperty;

/**
 * The property point.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class PropertyPoint
{
    use PointTrait;

    /**
     * @var ReflectionProperty $reflector
     */
    private $reflector;

    /**
     * Constructs the property point.
     *
     * @param string $fqn
     * @throws InvalidPropertyPointException
     * @throws InvalidPropertyPointSyntaxException
     */
    public function __construct(string $fqn)
    {
        $regex = '/^([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/';

        if (!\preg_match($regex, $fqn, $matches)) {
            throw new InvalidPropertyPointSyntaxException(\sprintf(
                '"%s" is not a property point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                $regex
            ));
        }

        list($matchedFqn, $matchedClassName, $matchedName) = $matches;

        try {
            $reflector = new ReflectionProperty($matchedClassName, $matchedName);
        } catch (ReflectionException $exception) {
            throw new InvalidPropertyPointException(\sprintf(
                '"%s" is not a property point. %s.',
                $fqn,
                $exception->getMessage()
            ));
        }

        $reflector->setAccessible(true);

        $this->reflector = $reflector;
        $this->fqn = $fqn;
        $this->classFqn = $matchedClassName;
        $this->name = $matchedName;
    }

    /**
     * Gets the point value from the passed object.
     *
     * @param object $object
     * @return mixed
     * @throws InvalidOperationException
     */
    public function getValue(object $object)
    {
        try {
            return $this->reflector->getValue($object);
        } catch (ReflectionException $exception) {
            throw new InvalidOperationException(\sprintf(
                'Invalid "%s" operation. %s',
                __METHOD__,
                $exception->getMessage()
            ));
        }
    }

    /**
     * Sets the point value on the passed object.
     *
     * @param object $object
     * @param mixed $value
     * @return object
     * @throws InvalidOperationException
     */
    public function setValue(object $object, $value): object
    {
        try {
            $this->reflector->setValue($object, $value);
        } catch (ReflectionException $exception) {
            throw new InvalidOperationException(\sprintf(
                'Invalid "%s" operation. %s',
                __METHOD__,
                $exception->getMessage()
            ));
        }

        return $object;
    }
}
