<?php

namespace Opportus\ObjectMapper\Benchmarks\Src;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;
use Opportus\ObjectMapper\ObjectMapper;

class ObjectMapperBench
{
    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithAutomaticMapping()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);
        $objectMapper = new ObjectMapper($mapBuilder);

        $source = new BenchObject(1);
        $source->setB(11);

        $target = $objectMapper->map($source, BenchObject::class);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithManualMapping()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);
        $objectMapper = new ObjectMapper($mapBuilder);

        $source = new BenchObject(1);
        $source->setB(11);

        $map = $mapBuilder
            ->addRoute(\sprintf('%s.getA()', BenchObject::class), \sprintf('%s.__construct().$a', BenchObject::class))
            ->addRoute(\sprintf('%s.getB()', BenchObject::class), \sprintf('%s.setB().$b', BenchObject::class))
            ->buildMap()
        ;

        $target = $objectMapper->map($source, BenchObject::class, $map);
    }
}
