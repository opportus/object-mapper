<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks\Src\Map;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;

/**
 * The map builder bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Src\Map
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 *
 * @BeforeMethods({"buildMapBuilder"})
 */
class MapBuilderBench
{
    private $mapBuilder;

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchBuildMapWithPathFindingStrategy()
    {
        $this->mapBuilder->buildMap(true);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchBuildMapWithNoPathFindingStrategy()
    {
        $this->mapBuilder
            ->addRoute(\sprintf('%s.getA()', BenchObject::class), \sprintf('%s.__construct().$a', BenchObject::class))
            ->addRoute(\sprintf('%s.getB()', BenchObject::class), \sprintf('%s.setB().$b', BenchObject::class))
            ->buildMap()
        ;
    }

    public function buildMapBuilder()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);

        $this->mapBuilder = $mapBuilder;
    }
}
