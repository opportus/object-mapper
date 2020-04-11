<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks\Src\Map\Route;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;

/**
 * The route builder bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Src\Map\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteBuilderBench
{
    private $routeBuilder;

    public function __construct()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);

        $this->routeBuilder = $routeBuilder;
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchBuildRoute()
    {
        $this->routeBuilder->buildRoute(\sprintf('%s.getA()', BenchObject::class), \sprintf('%s.__construct().$a', BenchObject::class));
        $this->routeBuilder->buildRoute(\sprintf('%s.getB()', BenchObject::class), \sprintf('%s.setB().$b', BenchObject::class));
    }
}
