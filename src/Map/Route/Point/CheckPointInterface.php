<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\ObjectMapperInterface;

/**
 * The check point interface.
 *
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface CheckPointInterface
{
    /**
     * Controls the value.
     *
     * @param mixed $value
     * @param Route $route
     * @param Context $context
     * @param ObjectMapperInterface $objectMapper
     * @return mixed
     */
    public function control($value, Route $route, Context $context, ObjectMapperInterface $objectMapper);
}
