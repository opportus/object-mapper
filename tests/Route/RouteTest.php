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

use Opportus\ObjectMapper\Point\CheckPointCollection;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\Tests\Test;

/**
 * The route test.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteTest extends Test
{
    /**
     * @dataProvider provideRoutePoints
     */
    public function testConstruct(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = $this->createRoute($sourcePoint, $targetPoint, $checkPoints);

        static::assertInstanceOf(RouteInterface::class, $route);
    }

    /**
     * @dataProvider provideRoutePoints
     */
    public function testGetFqn(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = $this->createRoute($sourcePoint, $targetPoint, $checkPoints);

        static::assertSame(
            \sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn()),
            $route->getFqn()
        );
    }

    /**
     * @dataProvider provideRoutePoints
     */
    public function testGetSourcePoint(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = $this->createRoute($sourcePoint, $targetPoint, $checkPoints);

        static::assertInstanceOf(
            \get_class($sourcePoint),
            $route->getSourcePoint()
        );

        static::assertSame(
            $sourcePoint->getFqn(),
            $route->getSourcePoint()->getFqn()
        );
    }

    /**
     * @dataProvider provideRoutePoints
     */
    public function testGetTargetPoint(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = $this->createRoute($sourcePoint, $targetPoint, $checkPoints);

        static::assertInstanceOf(
            \get_class($targetPoint),
            $route->getTargetPoint()
        );

        static::assertSame(
            $targetPoint->getFqn(),
            $route->getTargetPoint()->getFqn()
        );
    }

    /**
     * @dataProvider provideRoutePoints
     */
    public function testGetCheckPoints(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = $this->createRoute($sourcePoint, $targetPoint, $checkPoints);

        static::assertCount(\count($checkPoints), $route->getCheckPoints());
        static::assertSame($checkPoints, $route->getCheckPoints());
    }

    private function createRoute(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): Route {
        return new Route($sourcePoint, $targetPoint, $checkPoints);
    }
}
