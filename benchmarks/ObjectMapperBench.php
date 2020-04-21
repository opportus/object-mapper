<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks;

use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\ObjectMapper;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * The object mapper bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperBench
{
    private $objectMapper;
    private $noPathFindingMap;
    private $source;

    public function __construct()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);
        $objectMapper = new ObjectMapper($mapBuilder);

        $this->objectMapper = $objectMapper;

        $this->noPathFindingMap = $mapBuilder
            ->getRouteBuilder()
                ->setSourcePoint(\sprintf('%s.getA()', BenchObject::class))
                ->setTargetPoint(\sprintf('%s.__construct().$a', BenchObject::class))
                ->addRoute()
                ->getMapBuilder()
            ->getRouteBuilder()
                ->setSourcePoint(\sprintf('%s.getB()', BenchObject::class))
                ->setTargetPoint(\sprintf('%s.setB().$b', BenchObject::class))
                ->addRoute()
                ->getMapBuilder()
            ->getMap();

        $this->source = new BenchObject(1);
        $this->source->setB(11);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithPathFinding()
    {
        $this->objectMapper->map($this->source, BenchObject::class);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithNoPathFinding()
    {
        $this->objectMapper->map(
            $this->source,
            BenchObject::class,
            $this->noPathFindingMap
        );
    }
}
