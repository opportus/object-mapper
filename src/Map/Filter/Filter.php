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
 * The filter.
 *
 * @package Opportus\ObjectMapper\Map\Filter
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class Filter implements FilterInterface
{
    /**
     * @var Route $route
     */
    private $route;

    /**
     * @var callable $callable
     */
    private $callable;

    /**
     * Constructs the filter.
     *
     * @param Route $route
     *
     * @param callable Returns a mixed value which will be assigned to the target point by the mapper. The callable takes 3 arguments:
     *
     * - `Opportus\ObjectMapper\Map\Route\Route` The route that the filter supports.
     * - `Opportus\ObjectMapper\Context` The context of the current mapping.
     * - `Opportus\ObjectMapper\ObjectMapperInterface` The object mapper service, useful for recursion.
     */
    public function __construct(Route $route, callable $callable)
    {
        $this->route = $route;
        $this->callable = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function supportRoute(Route $route): bool
    {
        return $route->getFqn() === $this->route->getFqn();
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(Route $route, Context $context, ObjectMapperInterface $objectMapper)
    {
        return ($this->callable)($route, $context, $objectMapper);
    }
}
