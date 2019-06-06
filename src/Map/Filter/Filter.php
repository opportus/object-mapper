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
use Opportus\ObjectMapper\Exception\InvalidOperationException;
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
     * @param callable Returns a mixed value which will be assigned to the target point by the mapper. The callable takes 2 arguments:
     *
     * - `Opportus\ObjectMapper\Map\Route\Route` The route the filter is on.
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
    public function getRouteFqn(): string
    {
        return $this->route->getFqn();
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(Context $context, ObjectMapperInterface $objectMapper)
    {
        if ($context->getSourceClassFqn() !== $this->route->getSourcePoint()->getClassFqn()) {
            throw new InvalidOperationException(\sprintf(
                'Operation "%s" is invalid. Expecting the source class FQN "%s" to match the source point class FQN "%s".',
                __METHOD__,
                $context->getSourceClassFqn(),
                $this->route->getSourcePoint()->getClassFqn()
            ));
        }

        if ($context->getTargetClassFqn() !== $this->route->getTargetPoint()->getClassFqn()) {
            throw new InvalidOperationException(\sprintf(
                'Operation "%s" is invalid. Expecting the target class FQN "%s" to match the target point class FQN "%s".',
                __METHOD__,
                $context->getTargetClassFqn(),
                $this->route->getTargetPoint()->getClassFqn()
            ));
        }

        return $this->callable($route, $context, $objectMapper);
    }
}
