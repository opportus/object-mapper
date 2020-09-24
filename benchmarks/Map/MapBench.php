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
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
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
    private $pathFinderMap;
    private $pathFinderSource;
    private $pathFinderTarget;

    private $noPathFinderMap;
    private $noPathFinderSource;
    private $noPathFinderTarget;

    public function __construct()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);

        $source = new BenchObject(1);
        $source->setB(11);

        $this->pathFinderMap = $mapBuilder
            ->addPathFinder(new StaticPathFinder($routeBuilder))
            ->getMap();

        $this->pathFinderSource = new Source($source);
        $this->pathFinderTarget = new Target(BenchObject::class);

        $this->noPathFinderMap = $mapBuilder
            ->getRouteBuilder()
                ->setStaticSourcePoint(\sprintf('%s.getA()', BenchObject::class))
                ->setStaticTargetPoint(\sprintf('%s.__construct().$a', BenchObject::class))
                ->addRouteToMapBuilder()
                ->setStaticSourcePoint(\sprintf('%s.getB()', BenchObject::class))
                ->setStaticTargetPoint(\sprintf('%s.setB().$b', BenchObject::class))
                ->addRouteToMapBuilder()
                ->getMapBuilder()
            ->getMap();

        $this->noPathFinderSource = new Source($source);
        $this->noPathFinderTarget = new Target(BenchObject::class);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithPathFinder()
    {
        $this->pathFinderMap->getRoutes(
            $this->pathFinderSource,
            $this->pathFinderTarget
        );
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithNoPathFinder()
    {
        $this->noPathFinderMap->getRoutes(
            $this->noPathFinderSource,
            $this->noPathFinderTarget
        );
    }
}
