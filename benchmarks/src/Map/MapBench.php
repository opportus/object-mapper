<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks\Src\Map;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;

/**
 * The map bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Src\Map
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MapBench
{
    private $pathFindingStrategyMap;
    private $pathFindingStrategyContext;

    private $noPathFindingStrategyMap;
    private $noPathFindingStrategyContext;

    public function __construct()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);

        $source = new BenchObject(1);
        $source->setB(11);

        $this->pathFindingStrategyMap = $mapBuilder->buildMap(true);

        $this->pathFindingStrategyContext = new Context($source, BenchObject::class, $this->pathFindingStrategyMap);

        $this->noPathFindingStrategyMap = $mapBuilder
            ->addRoute(\sprintf('%s.getA()', BenchObject::class), \sprintf('%s.__construct().$a', BenchObject::class))
            ->addRoute(\sprintf('%s.getB()', BenchObject::class), \sprintf('%s.setB().$b', BenchObject::class))
            ->buildMap()
        ;

        $this->noPathFindingStrategyContext = new Context($source, BenchObject::class, $this->noPathFindingStrategyMap);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithPathFindingStrategy()
    {
        $this->pathFindingStrategyMap->getRoutes($this->pathFindingStrategyContext);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithNoPathFindingStrategy()
    {
        $this->noPathFindingStrategyMap->getRoutes($this->noPathFindingStrategyContext);
    }
}
