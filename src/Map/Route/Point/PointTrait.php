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

/**
 * The point trait.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
trait PointTrait
{
    /**
     * @var \Reflector $reflector
     */
    private $reflector;

    /**
     * @var string $fqn
     */
    private $fqn;

    /**
     * Gets the Fully Qualified Name of the point.
     *
     * @return string
     */
    public function getFqn(): string
    {
        return $this->fqn;
    }

    /**
     * Gets the Fully Qualified Name of the class of the point.
     *
     * @return string
     */
    public function getClassFqn(): string
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
     * @param string $reflectorPropertyName
     * @return mixed
     */
    public function __get($reflectorPropertyName)
    {
        return $this->reflector->$reflectorPropertyName;
    }

    /**
     * @param string $reflectorMethodName
     * @param array $reflectorMethodArguments
     * @return mixed
     */
    public function __call($reflectorMethodName, $reflectorMethodArguments)
    {
        return $this->reflector->$reflectorMethodName(...$reflectorMethodArguments);
    }
}
