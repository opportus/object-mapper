<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Filter;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\ObjectMapperInterface;

/**
 * The filter interface.
 *
 * Implement this to filter the source point value before it is assigned to the target point.
 *
 * @package Opportus\ObjectMapper\Map\Filter
 * @author Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface FilterInterface
{
    /**
     * Checks whether this filter supports the passed route or not.
     *
     * @param Route $route
     * @return bool
     */
    public function supportRoute(Route $route): bool;

    /**
     * Gets the value to assign to the target point.
     *
     * @param Route $route
     * @param Context $context
     * @param ObjectMapperInterface $objectMapper
     * @return mixed
     */
    public function getValue(Route $route, Context $context, ObjectMapperInterface $objectMapper);
}
