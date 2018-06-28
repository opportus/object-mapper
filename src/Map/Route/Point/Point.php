<?php

namespace Opportus\ObjectMapper\Map\Route\Point;

/**
 * The base point.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
abstract class Point implements PointInterface
{
    /**
     * @var \Reflector $reflector
     */
    protected $reflector;

    /**
     * @var string $fqn
     */
    protected $fqn;

    /**
     * {@inheritdoc}
     */
    public function getFqn() : string
    {
        return $this->fqn;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassFqn() : string
    {
        return $this->reflector->getDeclaringClass()->getName();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->fqn;
    }

    /**
     * @param  string $reflectorPropertyName
     * @return mixed
     */
    public function __get($reflectorPropertyName)
    {
        return $this->reflector->$reflectorPropertyName;
    }

    /**
     * @param  string $reflectorMethodName
     * @param  array $reflectorMethodArguments
     * @return mixed
     */
    public function __call($reflectorMethodName, $reflectorMethodArguments)
    {
        return $this->reflector->$reflectorMethodName(...$reflectorMethodArguments);
    }
}

