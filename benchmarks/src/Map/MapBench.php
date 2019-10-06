<?php

namespace Opportus\ObjectMapper\Benchmarks\Src\Map;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;

class MapBench
{
    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithPathFindingStrategy()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);

        $map = $mapBuilder->buildMap(true);

        $source = new BenchObject(1);
        $source->setB(11);

        $context = new Context($source, BenchObject::class, $map);

        $routes = $map->getRoutes($context);
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchGetRoutesWithNoPathFindingStrategy()
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);

        $map = $mapBuilder
            ->addRoute(\sprintf('%s.getA()', BenchObject::class), \sprintf('%s.__construct().$a', BenchObject::class))
            ->addRoute(\sprintf('%s.getB()', BenchObject::class), \sprintf('%s.setB().$b', BenchObject::class))
            ->buildMap()
        ;

        $source = new BenchObject(1);
        $source->setB(11);

        $context = new Context($source, BenchObject::class, $map);

        $routes = $map->getRoutes($context);
    }
}
