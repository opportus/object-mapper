<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Map\Route\Point\PointFactoryInterface;

/**
 * The route builder.
 *
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class RouteBuilder implements RouteBuilderInterface
{
    /**
     * @var PointFactoryInterface $pointFactory
     */
    private $pointFactory;

    /**
     * Constructs the route builder.
     *
     * @param PointFactoryInterface $pointFactory
     */
    public function __construct(PointFactoryInterface $pointFactory)
    {
        $this->pointFactory = $pointFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildRoute(
        string $sourcePointFqn,
        string $targetPointFqn,
        CheckPointCollection $checkPoints
    ): Route {
        $sourcePoint = $this->pointFactory->createPoint($sourcePointFqn);
        $targetPoint = $this->pointFactory->createPoint($targetPointFqn);

        return new Route($sourcePoint, $targetPoint, $checkPoints);
    }
}
