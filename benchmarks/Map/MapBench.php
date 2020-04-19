<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks\Map;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * The map bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapBench
{
    private $pathFindingMap;
    private $pathFindingSource;
    private $pathFindingTarget;

    private $noPathFindingMap;
    private $noPathFindingSource;
    private $noPathFindingTarget;

    public function __construct()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);

        $source = new BenchObject(1);
        $source->setB(11);

        $this->pathFindingMap = $mapBuilder->buildMap(true);

        $this->pathFindingSource = new Source($source);
        $this->pathFindingTarget = new Target(BenchObject::class);

        $this->noPathFindingMap = $mapBuilder
            ->addRoute(
                \sprintf('%s.getA()', BenchObject::class),
                \sprintf('%s.__construct().$a', BenchObject::class)
            )
            ->addRoute(
                \sprintf('%s.getB()', BenchObject::class),
                \sprintf('%s.setB().$b', BenchObject::class)
            )
            ->buildMap();

        $this->noPathFindingSource = new Source($source);
        $this->noPathFindingTarget = new Target(BenchObject::class);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithPathFinding()
    {
        $this->pathFindingMap->getRoutes(
            $this->pathFindingSource,
            $this->pathFindingTarget
        );
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithNoPathFinding()
    {
        $this->noPathFindingMap->getRoutes(
            $this->noPathFindingSource,
            $this->noPathFindingTarget
        );
    }
}
