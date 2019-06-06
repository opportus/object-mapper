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

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Exception\NotSupportedContextException;
use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\ObjectMapperInterface;

/**
 * The filter interface.
 * 
 * Use this to filter the source point value before it is assigned to the target point.
 *
 * @package Opportus\ObjectMapper\Map\Filter
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface FilterInterface
{
    /**
     * Gets the Fully Qualified Name of the route.
     *
     * @return string
     */
    public function getRouteFqn(): string;

    /**
     * Gets the value.
     *
     * Usage example:
     *
     * `return (bool) $this->route->getSourcePoint()->getValue($context->getSource());`
     *
     * @param Context $context
     * @param ObjectMapperInterface $objectMapper
     * @return mixed
     * @throws InvalidOperationException
     * @throws NotSupportedContextException When this is thrown, the mapper assigns the original source point value to the target point
     */
    public function getValue(Context $context, ObjectMapperInterface $objectMapper);
}
