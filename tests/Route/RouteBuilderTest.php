<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Route;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The route builder test.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteBuilderTest extends FinalBypassTestCase
{
    public function testConstruct()
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder);
        static::assertInstanceOf(RouteBuilderInterface::class, $routeBuilder);
    }

    /**
     * @dataProvider providePoints
     * @param string $sourcePointFqn
     * @param string $targetPointFqn
     * @param CheckPointInterface $checkPoint
     */
    public function testGetRoute(
        string $sourcePointFqn,
        string $targetPointFqn,
        CheckPointInterface $checkPoint
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        $route = $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint($checkPoint, 10)
            ->getRoute();

        static::assertInstanceOf(Route::class, $route);

        static::assertSame(
            $sourcePointFqn,
            $route->getSourcePoint()->getFqn()
        );

        static::assertSame(
            $targetPointFqn,
            $route->getTargetPoint()->getFqn()
        );

        static::assertCount(1, $route->getCheckPoints());

        foreach ($route->getCheckPoints() as $position => $checkPoint) {
            static::assertEquals(10, $position);
        }
    }

    public function testGetRouteException(): void
    {
        $this->expectException(InvalidOperationException::class);

        $this->buildRouteBuilder()->getRoute();
    }

    /**
     * @dataProvider providePoints
     * @param string $sourcePointFqn
     * @param string $targetPointFqn
     * @param CheckPointInterface $checkPoint
     */
    public function testAddRouteToMapBuilder(
        string $sourcePointFqn,
        string $targetPointFqn,
        CheckPointInterface $checkPoint
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        $mapBuilder = $routeBuilder
            ->setMapBuilder($this->buildMapBuilder())
            ->setSourcePoint($sourcePointFqn)
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint($checkPoint, 10)
            ->addRouteToMapBuilder();

        static::assertInstanceOf(MapBuilderInterface::class, $mapBuilder);
    }

    public function testAddRouteToMapBuilderException(): void
    {
        self::expectException(InvalidOperationException::class);

        $this->buildRouteBuilder()->addRouteToMapBuilder();
    }

    public function providePoints(): array
    {
        return [
            [
                \sprintf(
                    '%s.$property',
                    RouteBuilderTestClass::class
                ),
                \sprintf(
                    '%s.$property',
                    RouteBuilderTestClass::class
                ),
                new CheckPointTestClass()
            ],
            [
                \sprintf(
                    '%s.$property',
                    RouteBuilderTestClass::class
                ),
                \sprintf(
                    '%s.method().$parameter',
                    RouteBuilderTestClass::class
                ),
                new CheckPointTestClass()
            ],
            [
                \sprintf(
                    '%s.method()',
                    RouteBuilderTestClass::class
                ),
                \sprintf(
                    '%s.$property',
                    RouteBuilderTestClass::class
                ),
                new CheckPointTestClass()
            ],
            [
                \sprintf(
                    '%s.method()',
                    RouteBuilderTestClass::class
                ),
                \sprintf(
                    '%s.method().$parameter',
                    RouteBuilderTestClass::class
                ),
                new CheckPointTestClass()
            ],
        ];
    }

    private function buildRouteBuilder(): RouteBuilder
    {
        return new RouteBuilder(new PointFactory());
    }

    private function buildMapBuilder(): MapBuilder
    {
        return new MapBuilder($this->buildRouteBuilder());
    }
}

/**
 * The route builder test class.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteBuilderTestClass
{
    public $property;

    public function method($parameter = null)
    {
    }
}

/**
 * The checkpoint test class.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class CheckPointTestClass implements CheckPointInterface
{
    public function control(
        $value,
        Route $route,
        Source $source,
        Target $target
    ) {
    }
}
