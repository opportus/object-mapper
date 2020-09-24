<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

/**
 * The object point interface.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface ObjectPointInterface
{
    /**
     * Gets the Fully Qualified Name of the point.
     *
     * @return string
     */
    public function getFqn(): string;

    /**
     * Gets the Fully Qualified Name of the class of the point.
     *
     * @return string
     */
    public function getClassFqn(): string;

    /**
     * Gets the name of the point.
     *
     * @return string
     */
    public function getName(): string;
}