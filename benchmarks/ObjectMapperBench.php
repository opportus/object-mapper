<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks;

use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\ObjectMapper;
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
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
    private $noPathFinderMap;
    private $source;

    public function __construct()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);
        $objectMapper = new ObjectMapper($mapBuilder);

        $this->objectMapper = $objectMapper;

        $this->noPathFinderMap = $mapBuilder
            ->getRouteBuilder()
                ->setStaticSourcePoint(\sprintf('%s::getA()', BenchObject::class))
                ->setStaticTargetPoint(\sprintf('%s::__construct()::$a', BenchObject::class))
                ->addRouteToMapBuilder()
                ->setStaticSourcePoint(\sprintf('%s::getB()', BenchObject::class))
                ->setStaticTargetPoint(\sprintf('%s::setB()::$b', BenchObject::class))
                ->addRouteToMapBuilder()
                ->getMapBuilder()
            ->getMap();

        $this->source = new BenchObject(1);
        $this->source->setB(11);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithPathFinder()
    {
        $this->objectMapper->map(
            $this->source,
            BenchObject::class
        );
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithNoPathFinder()
    {
        $this->objectMapper->map(
            $this->source,
            BenchObject::class,
            $this->noPathFinderMap
        );
    }
}
