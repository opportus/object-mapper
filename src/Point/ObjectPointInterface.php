<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
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
     * Gets the Fully Qualified Name regex pattern of the point.
     *
     * @return string The Fully Qualified Name regex pattern of the point
     */
    public static function getFqnRegexPattern(): string;

    /**
     * Gets the Fully Qualified Name of the point.
     *
     * @return string The Fully Qualified Name of the point
     */
    public function getFqn(): string;

    /**
     * Gets the name of the point.
     *
     * @return string The name of the point
     */
    public function getName(): string;
}
