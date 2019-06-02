<?php

namespace Opportus\ObjectMapper\Map\Filter;

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
     * @param Opportus\ObjectMapper\Context $context
     * @param Opportus\ObjectMapper\ObjectMapperInterface $objectMapper
     * @return mixed
     * @throws Opportus\ObjectMapper\Exception\NotSupportedContextException When this is thrown, the mapper assigns the original source point value to the target point
     * @throws Opportus\ObjectMapper\Exception\InvalidOperationException
     */
    public function getValue(Context $context, ObjectMapperInterface $objectMapper);
}
