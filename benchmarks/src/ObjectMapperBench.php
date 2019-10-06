<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks\Src;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;
use Opportus\ObjectMapper\ObjectMapper;

/**
 * The object mapper bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Src
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperBench
{
    private $objectMapper;
    private $noPathFindingStrategyMap;
    private $source;

    public function __construct()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);
        $objectMapper = new ObjectMapper($mapBuilder);

        $this->objectMapper = $objectMapper;

        $this->noPathFindingStrategyMap = $mapBuilder
            ->addRoute(\sprintf('%s.getA()', BenchObject::class), \sprintf('%s.__construct().$a', BenchObject::class))
            ->addRoute(\sprintf('%s.getB()', BenchObject::class), \sprintf('%s.setB().$b', BenchObject::class))
            ->buildMap()
        ;

        $this->source = new BenchObject(1);
        $this->source->setB(11);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithPathFindingStrategy()
    {
        $this->objectMapper->map($this->source, BenchObject::class);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithNoPathFindingStrategy()
    {
        $this->objectMapper->map($this->source, BenchObject::class, $this->noPathFindingStrategy);
    }
}