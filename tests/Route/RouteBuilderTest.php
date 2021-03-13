<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\IterableRecursionCheckPoint;
use Opportus\ObjectMapper\Point\RecursionCheckPoint;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Tests\Test;
use Opportus\ObjectMapper\Tests\TestObjectA;
use Opportus\ObjectMapper\Tests\TestObjectB;

/**
 * The route builder test.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteBuilderTest extends Test
{
    public function testConstruct()
    {
        $routeBuilder = $this->createRouteBuilder();

        static::assertInstanceOf(RouteBuilderInterface::class, $routeBuilder);
    }

    public function testSetMapBuilder(): void
    {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->setMapBuilder(
            $this->createMapBuilder()
        );

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideStaticSourcePointFqn
     */
    public function testSetStaticSourcePoint(string $pointFqn): void
    {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->setStaticSourcePoint($pointFqn);

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideInvalidStaticSourcePointFqn
     */
    public function testSetStaticSourcePointException(string $pointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setStaticSourcePoint($pointFqn);
    }

    /**
     * @dataProvider provideStaticTargetPointFqn
     */
    public function testSetStaticTargetPoint(string $pointFqn): void
    {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->setStaticTargetPoint($pointFqn);

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideInvalidStaticTargetPointFqn
     */
    public function testSetStaticTargetPointException(string $pointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setStaticTargetPoint($pointFqn);
    }

    /**
     * @dataProvider provideDynamicSourcePointFqn
     */
    public function testSetDynamicSourcePoint(string $pointFqn): void
    {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->setDynamicSourcePoint($pointFqn);

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideInvalidDynamicSourcePointFqn
     */
    public function testSetDynamicSourcePointException(string $pointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setDynamicSourcePoint($pointFqn);
    }

    /**
     * @dataProvider provideDynamicTargetPointFqn
     */
    public function testSetDynamicTargetPoint(string $pointFqn): void
    {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->setDynamicTargetPoint($pointFqn);

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideInvalidDynamicTargetPointFqn
     */
    public function testSetDynamicTargetPointException(string $pointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setDynamicTargetPoint($pointFqn);
    }

    /**
     * @dataProvider provideSourcePointFqn
     */
    public function testSetSourcePoint(string $pointFqn): void
    {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->setSourcePoint($pointFqn);

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideInvalidSourcePointFqn
     */
    public function testSetSourcePointException(string $pointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setSourcePoint($pointFqn);
    }

    /**
     * @dataProvider provideTargetPointFqn
     */
    public function testSetTargetPoint(string $pointFqn): void
    {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->setTargetPoint($pointFqn);

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideInvalidTargetPointFqn
     */
    public function testSetTargetPointException(string $pointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->setTargetPoint($pointFqn);
    }

    public function testAddCheckPoint(): void
    {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->addCheckPoint(
            $this->getMockBuilder(CheckPointInterface::class)->getMock()
        );

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideSourcePointFqn
     */
    public function testAddRecursionCheckPoint(
        string $targetSourcePointFqn
    ): void {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->addRecursionCheckPoint(
            TestObjectA::class,
            TestObjectB::class,
            $targetSourcePointFqn
        );

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideInvalidSourcePointFqn
     */
    public function testAddRecursionCheckPointException(
        string $targetSourcePointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->addRecursionCheckPoint(
            TestObjectA::class,
            TestObjectB::class,
            $targetSourcePointFqn
        );
    }

    /**
     * @dataProvider provideSourcePointFqn
     */
    public function testAddIterableRecursionCheckPoint(
        string $targetSourcePointFqn
    ): void {
        $routeBuilder1 = $this->createRouteBuilder();
        $routeBuilder2 = $routeBuilder1->addIterableRecursionCheckPoint(
            TestObjectA::class,
            TestObjectB::class,
            $targetSourcePointFqn
        );

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider provideInvalidSourcePointFqn
     */
    public function testAddIterableRecursionCheckPointException(
        string $targetIterableSourcePointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidArgumentException::class);

        $routeBuilder->addIterableRecursionCheckPoint(
            TestObjectA::class,
            TestObjectB::class,
            $targetIterableSourcePointFqn
        );
    }

    /**
     * @dataProvider providePointsFqn
     * @depends testSetSourcePoint
     * @depends testSetTargetPoint
     * @depends testAddCheckPoint
     * @depends testAddRecursionCheckPoint
     * @depends testAddIterableRecursionCheckPoint
     */
    public function testGetRoute(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();

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
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn,
                40
            )
            ->addRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn
            )
            ->addIterableRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn,
                10
            )
            ->addIterableRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn
            )
            ->getRoute();

        static::assertInstanceOf(Route::class, $route);

        static::assertSame(
            \ltrim($sourcePointFqn, '#~'),
            \ltrim($route->getSourcePoint()->getFqn(), '#~')
        );

        static::assertSame(
            \ltrim($targetPointFqn, '#~'),
            \ltrim($route->getTargetPoint()->getFqn(), '#~')
        );

        static::assertCount(7, $route->getCheckPoints());

        static::assertArrayHasKey(10, $route->getCheckPoints());
        static::assertArrayHasKey(20, $route->getCheckPoints());
        static::assertArrayHasKey(30, $route->getCheckPoints());
        static::assertArrayHasKey(31, $route->getCheckPoints());
        static::assertArrayHasKey(40, $route->getCheckPoints());
        static::assertArrayHasKey(41, $route->getCheckPoints());
        static::assertArrayHasKey(42, $route->getCheckPoints());

        static::assertArrayNotHasKey(50, $route->getCheckPoints());

        static::assertInstanceOf(
            IterableRecursionCheckPoint::class,
            $route->getCheckPoints()[10]
        );

        static::assertInstanceOf(
            CheckPointInterface::class,
            $route->getCheckPoints()[20]
        );

        static::assertInstanceOf(
            CheckPointInterface::class,
            $route->getCheckPoints()[30]
        );

        static::assertInstanceOf(
            CheckPointInterface::class,
            $route->getCheckPoints()[31]
        );

        static::assertInstanceOf(
            RecursionCheckPoint::class,
            $route->getCheckPoints()[40]
        );

        static::assertInstanceOf(
            RecursionCheckPoint::class,
            $route->getCheckPoints()[41]
        );

        static::assertInstanceOf(
            IterableRecursionCheckPoint::class,
            $route->getCheckPoints()[42]
        );

        $expectedCheckPointPositions = [
            10,
            20,
            30,
            31,
            40,
            41,
            42,
        ];

        $i = 0;

        foreach ($route->getCheckPoints() as $checkPointPosition => $checkPoint) {
            static::assertEquals(
                $expectedCheckPointPositions[$i],
                $checkPointPosition
            );

            $i++;
        }
    }

    public function testGetRouteException0(): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder->getRoute();
    }

    /**
     * @dataProvider provideSourcePointFqn
     * @depends testSetSourcePoint
     */
    public function testGetRouteException1(string $sourcePointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->getRoute();
    }

    /**
     * @dataProvider provideTargetPointFqn
     * @depends testSetTargetPoint
     */
    public function testGetRouteException2(string $targetPointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->getRoute();
    }

    /**
     * @depends testAddCheckPoint
     */
    public function testGetRouteException3(): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->getRoute();
    }

    /**
     * @dataProvider provideSourcePointFqn
     * @depends testSetSourcePoint
     * @depends testAddCheckPoint
     */
    public function testGetRouteException4(string $sourcePointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->getRoute();
    }

    /**
     * @dataProvider provideTargetPointFqn
     * @depends testSetTargetPoint
     * @depends testAddCheckPoint
     */
    public function testGetRouteException5(string $targetPointFqn): void
    {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->getRoute();
    }

    /**
     * @dataProvider providePointsFqn
     * @depends testSetMapBuilder
     * @depends testSetSourcePoint
     * @depends testSetTargetPoint
     * @depends testAddCheckPoint
     * @depends testAddRecursionCheckPoint
     * @depends testAddIterableRecursionCheckPoint
     */
    public function testAddRouteToMapBuilder(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder1 = $this->createRouteBuilder();
        $mapBuilder = $this->createMapBuilder();

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
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn,
                40
            )
            ->addRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn
            )
            ->addIterableRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn,
                10
            )
            ->addIterableRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder();

        static::assertInstanceOf(RouteBuilder::class, $routeBuilder2);
        static::assertNotEquals($routeBuilder1, $routeBuilder2);
    }

    /**
     * @dataProvider providePointsFqn
     * @depends testSetSourcePoint
     * @depends testSetTargetPoint
     */
    public function testAddRouteToMapBuilderFirstException(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->setTargetPoint($targetPointFqn)
            ->addRouteToMapBuilder();
    }

    /**
     * @depends testSetMapBuilder
     */
    public function testAddRouteToMapBuilderSecondException0(): void
    {
        $routeBuilder = $this->createRouteBuilder();
        $mapBuilder = $this->createMapBuilder();

        $routeBuilder = $routeBuilder->setMapBuilder($mapBuilder);

        $this->expectException(InvalidOperationException::class);
        $routeBuilder->addRouteToMapBuilder();
    }

    /**
     * @dataProvider provideSourcePointFqn
     * @depends testSetMapBuilder
     * @depends testSetSourcePoint
     */
    public function testAddRouteToMapBuilderSecondException1(
        string $sourcePointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();
        $mapBuilder = $this->createMapBuilder();

        $routeBuilder = $routeBuilder->setMapBuilder($mapBuilder);

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addRouteToMapBuilder();
    }

    /**
     * @dataProvider provideTargetPointFqn
     * @depends testSetMapBuilder
     * @depends testSetTargetPoint
     */
    public function testAddRouteToMapBuilderSecondException2(
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();
        $mapBuilder = $this->createMapBuilder();

        $routeBuilder = $routeBuilder->setMapBuilder($mapBuilder);

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addRouteToMapBuilder();
    }

    /**
     * @dataProvider provideSourcePointFqn
     * @depends testSetMapBuilder
     * @depends testAddCheckPoint
     */
    public function testAddRouteToMapBuilderSecondException3(): void
    {
        $routeBuilder = $this->createRouteBuilder();
        $mapBuilder = $this->createMapBuilder();

        $routeBuilder = $routeBuilder->setMapBuilder($mapBuilder);

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();
    }

    /**
     * @dataProvider provideSourcePointFqn
     * @depends testSetMapBuilder
     * @depends testSetSourcePoint
     * @depends testAddCheckPoint
     */
    public function testAddRouteToMapBuilderSecondException4(
        string $sourcePointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();
        $mapBuilder = $this->createMapBuilder();

        $routeBuilder = $routeBuilder->setMapBuilder($mapBuilder);

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setSourcePoint($sourcePointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();
    }

    /**
     * @dataProvider provideTargetPointFqn
     * @depends testSetMapBuilder
     * @depends testSetTargetPoint
     * @depends testAddCheckPoint
     */
    public function testAddRouteToMapBuilderSecondException5(
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();
        $mapBuilder = $this->createMapBuilder();

        $routeBuilder = $routeBuilder->setMapBuilder($mapBuilder);

        $this->expectException(InvalidOperationException::class);
        $routeBuilder
            ->setTargetPoint($targetPointFqn)
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRouteToMapBuilder();
    }

    /**
     * @dataProvider providePointsFqn
     * @depends testSetMapBuilder
     * @depends testSetSourcePoint
     * @depends testSetTargetPoint
     * @depends testAddCheckPoint
     * @depends testAddRecursionCheckPoint
     * @depends testAddIterableRecursionCheckPoint
     * @depends testAddRouteToMapBuilder
     */
    public function testGetMapBuilder(
        string $sourcePointFqn,
        string $targetPointFqn
    ): void {
        $routeBuilder = $this->createRouteBuilder();
        $mapBuilder1 = $this->createMapBuilder();

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
            ->addCheckPoint(
                $this->getMockBuilder(CheckPointInterface::class)->getMock()
            )
            ->addRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn,
                40
            )
            ->addRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn
            )
            ->addIterableRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn,
                10
            )
            ->addIterableRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $sourcePointFqn
            )
            ->addRouteToMapBuilder()
            ->getMapBuilder();

        static::assertNotEquals($mapBuilder1, $mapBuilder2);
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
}
