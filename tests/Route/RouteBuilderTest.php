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

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\IterableRecursionCheckPoint;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Point\RecursionCheckPoint;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Tests\ObjectA;
use Opportus\ObjectMapper\Tests\ObjectB;
use Opportus\ObjectMapper\Tests\PointProviderTrait;
use PHPUnit\Framework\TestCase;

/**
 * The route builder test.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteBuilderTest extends TestCase
{
    use PointProviderTrait;

    public function testConstruct()
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        static::assertInstanceOf(RouteBuilderInterface::class, $routeBuilder);
    }

    public function testSetMapBuilder(): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->setMapBuilder($this->buildMapBuilder())
        );
    }

    /**
     * @dataProvider provideStaticSourcePointFqn
     */
    public function testSetStaticSourcePoint(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->setStaticSourcePoint($pointFqn)
        );
    }

    /**
     * @dataProvider provideInvalidStaticSourcePointFqn
     */
    public function testSetStaticSourcePointException(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setStaticSourcePoint($pointFqn);
    }

    /**
     * @dataProvider provideStaticTargetPointFqn
     */
    public function testSetStaticTargetPoint(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->setStaticTargetPoint($pointFqn)
        );
    }

    /**
     * @dataProvider provideInvalidStaticTargetPointFqn
     */
    public function testSetStaticTargetPointException(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setStaticTargetPoint($pointFqn);
    }

    /**
     * @dataProvider provideDynamicSourcePointFqn
     */
    public function testSetDynamicSourcePoint(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->setDynamicSourcePoint($pointFqn)
        );
    }

    /**
     * @dataProvider provideInvalidDynamicSourcePointFqn
     */
    public function testSetDynamicSourcePointException(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setDynamicSourcePoint($pointFqn);
    }

    /**
     * @dataProvider provideDynamicTargetPointFqn
     */
    public function testSetDynamicTargetPoint(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->setDynamicTargetPoint($pointFqn)
        );
    }

    /**
     * @dataProvider provideInvalidDynamicTargetPointFqn
     */
    public function testSetDynamicTargetPointException(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setDynamicTargetPoint($pointFqn);
    }

    /**
     * @dataProvider provideSourcePointFqn
     */
    public function testSetSourcePoint(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->setSourcePoint($pointFqn)
        );
    }

    /**
     * @dataProvider provideInvalidSourcePointFqn
     */
    public function testSetSourcePointException(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setSourcePoint($pointFqn);
    }

    /**
     * @dataProvider provideTargetPointFqn
     */
    public function testSetTargetPoint(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->setTargetPoint($pointFqn)
        );
    }

    /**
     * @dataProvider provideInvalidTargetPointFqn
     */
    public function testSetTargetPointException(string $pointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setTargetPoint($pointFqn);
    }

    public function testAddCheckPoint(): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
        );
    }

    /**
     * @dataProvider provideSourcePointFqn
     */
    public function testAddRecursionCheckPoint(
        string $targetSourcePointFqn
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $targetSourcePointFqn
            )
        );
    }

    /**
     * @dataProvider provideInvalidSourcePointFqn
     */
    public function testAddRecursionCheckPointException(
        string $targetSourcePointFqn
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->addRecursionCheckPoint(
            ObjectA::class,
            ObjectB::class,
            $targetSourcePointFqn
        );
    }

    /**
     * @dataProvider provideSourcePointFqn
     */
    public function testAddIterableRecursionCheckPoint(
        string $targetSourcePointFqn
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        static::assertNotSame(
            $routeBuilder,
            $routeBuilder->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $targetSourcePointFqn
            )
        );
    }

    /**
     * @dataProvider provideInvalidSourcePointFqn
     */
    public function testAddIterableRecursionCheckPointException(
        string $targetIterableSourcePointFqn
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->addIterableRecursionCheckPoint(
            ObjectA::class,
            ObjectB::class,
            $targetIterableSourcePointFqn
        );
    }

    /**
     * @dataProvider providePointsFqn
     */
    public function testGetRoute(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        $route = $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                20
            )
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                30
            )
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                10
            )
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn,
                40
            )
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn,
                10
            )
            ->getRoute();

        static::assertSame(
            \ltrim($sourcePointFqn, '#~'),
            \ltrim($route->getSourcePoint()->getFqn(), '#~')
        );

        static::assertSame(
            \ltrim($targetPointFqn, '#~'),
            \ltrim($route->getTargetPoint()->getFqn(), '#~')
        );

        static::assertCount(4, $route->getCheckPoints());

        static::assertArrayHasKey(10, $route->getCheckPoints());
        static::assertArrayHasKey(20, $route->getCheckPoints());
        static::assertArrayHasKey(30, $route->getCheckPoints());
        static::assertArrayHasKey(40, $route->getCheckPoints());

        static::assertArrayNotHasKey(50, $route->getCheckPoints());

        static::assertInstanceOf(IterableRecursionCheckPoint::class, $route->getCheckPoints()[10]);
        static::assertInstanceOf(CheckPointInterface::class, $route->getCheckPoints()[20]);
        static::assertInstanceOf(CheckPointInterface::class, $route->getCheckPoints()[30]);
        static::assertInstanceOf(RecursionCheckPoint::class, $route->getCheckPoints()[40]);

        $i = 1;

        foreach ($route->getCheckPoints() as $checkPointPosition => $checkPoint) {
            static::assertEquals($i*10, $checkPointPosition);

            $i++;
        }
    }

    /**
     * @dataProvider providePointsFqn
     */
    public function testGetRouteException(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->getRoute();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->getRoute();
    }

    /**
     * @dataProvider providePointsFqn
     */
    public function testAddRouteToMapBuilder(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder1 = $this->buildRouteBuilder();
        $mapBuilder = $this->buildMapBuilder();

        $routeBuilder2 = $routeBuilder1
            ->setMapBuilder($mapBuilder)
            ->setSourcePoint($sourcePointFqn)
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                20
            )
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                30
            )
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                10
            )
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn,
                40
            )
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn,
                10
            )
            ->addRouteToMapBuilder();

        static::assertNotSame($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider providePointsFqn
     */
    public function testAddRouteToMapBuilderException(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $routeBuilder = $routeBuilder->setMapBuilder(
            $this->buildMapBuilder()
        );

        $this->expectException(InvalidOperationException::class);
        $routeBuilder->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();
    }

    /**
     * @dataProvider providePointsFqn
     */
    public function testGetMapBuilder(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->buildRouteBuilder();
        $mapBuilder1 = $this->buildMapBuilder();

        $mapBuilder2 = $routeBuilder
            ->setMapBuilder($mapBuilder1)
            ->setSourcePoint($sourcePointFqn)
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                20
            )
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                30
            )
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                10
            )
            ->addRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn,
                40
            )
            ->addIterableRecursionCheckPoint(
                ObjectA::class,
                ObjectB::class,
                $sourcePointFqn,
                10
            )
            ->addRouteToMapBuilder()
            ->getMapBuilder();

        static::assertNotSame($mapBuilder1, $mapBuilder2);
    }

    public function provideAddRecursionCheckPointArguments(): array
    {
        $sourcePointFqns = $this->provideSourcePointFqn();
        $targetPointFqns = $this->provideTargetPointFqn();

        $arguments = [];

        foreach ($sourcePointFqns as $key => $sourcePointFqn) {
            $arguments[$key][0] = $sourcePointFqns[$key][0];
            $arguments[$key][1] = $targetPointFqns[$key][0];
            $arguments[$key][2] = $sourcePointFqns[$key][0];
        }

        return $arguments;
    }

    public function provideAddRecursionCheckPointInvalidArguments(): array
    {
        $sourcePointFqns = $this->provideInvalidSourcePointFqn();
        $targetPointFqns = $this->provideInvalidTargetPointFqn();

        $arguments = [];

        foreach ($sourcePointFqns as $key => $sourcePointFqn) {
            $arguments[$key][0] = $sourcePointFqns[$key][0];
            $arguments[$key][1] = $targetPointFqns[$key][0];
            $arguments[$key][2] = $sourcePointFqns[$key][0];
        }

        return $arguments;
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
