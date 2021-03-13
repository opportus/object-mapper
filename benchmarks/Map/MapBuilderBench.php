<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks\Map;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * The map builder bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
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
    public function benchBuildPathFinderMap()
    {
        $this->mapBuilder->getMap(true);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchBuildNoPathFinderMap()
    {
        $this->mapBuilder
            ->getRouteBuilder()
                ->setStaticSourcePoint(\sprintf('%s::getA()', BenchObject::class))
                ->setStaticTargetPoint(\sprintf('%s::__construct()::$a', BenchObject::class))
                ->addRouteToMapBuilder()
                ->setStaticSourcePoint(\sprintf('%s::getB()', BenchObject::class))
                ->setStaticTargetPoint(\sprintf('%s::setB()::$b', BenchObject::class))
                ->addRouteToMapBuilder()
                ->getMapBuilder()
            ->getMap();
    }

    public function buildMapBuilder()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);

        $this->mapBuilder = $mapBuilder;
    }
}
