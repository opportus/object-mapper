<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
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
     * @var string $fqn
     */
    private $fqn;

    /**
     * @var string $classFqn
     */
    private $classFqn;

    /**
     * @var string $name
     */
    private $name;

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
        return $this->classFqn;
    }

    /**
     * Gets the name of the point.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
