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
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;

/**
 * The map bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Src\Map
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapBench
{
    private $pathFindingStrategyMap;
    private $pathFindingStrategySource;
    private $pathFindingStrategyTarget;

    private $noPathFindingStrategyMap;
    private $noPathFindingStrategySource;
    private $noPathFindingStrategyTarget;

    public function __construct()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);

        $source = new BenchObject(1);
        $source->setB(11);

        $this->pathFindingStrategyMap = $mapBuilder->buildMap(true);

        $this->pathFindingStrategySource = new Source($source);
        $this->pathFindingStrategyTarget = new Target(BenchObject::class);

        $this->noPathFindingStrategyMap = $mapBuilder
            ->addRoute(
                \sprintf('%s.getA()', BenchObject::class),
                \sprintf('%s.__construct().$a', BenchObject::class),
                new CheckPointCollection()
            )
            ->addRoute(
                \sprintf('%s.getB()', BenchObject::class),
                \sprintf('%s.setB().$b', BenchObject::class),
                new CheckPointCollection()
            )
            ->buildMap();

        $this->noPathFindingStrategySource = new Source($source);
        $this->noPathFindingStrategyTarget = new Target(BenchObject::class);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithPathFindingStrategy()
    {
        $this->pathFindingStrategyMap->getRoutes(
            $this->pathFindingStrategySource,
            $this->pathFindingStrategyTarget
        );
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithNoPathFindingStrategy()
    {
        $this->noPathFindingStrategyMap->getRoutes(
            $this->noPathFindingStrategySource,
            $this->noPathFindingStrategyTarget
        );
    }
}
