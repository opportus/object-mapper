<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Map\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Tests\Map\Route\Point\MethodPointTest;
use Opportus\ObjectMapper\Tests\Map\Route\Point\ParameterPointTest;
use Opportus\ObjectMapper\Tests\Map\Route\Point\PropertyPointTest;
use PHPUnit\Framework\TestCase;

/**
 * The route test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteTest extends TestCase
{
    public function testRouteConstruction(): void
    {
        $sourcePoint = new PropertyPoint(\sprintf('%s::$privatePropertyToTest', PropertyPointTest::class));
        $targetPoint = new PropertyPoint(\sprintf('%s::$privatePropertyToTest', PropertyPointTest::class));
        $route = new Route($sourcePoint, $targetPoint);

        $this->assertEquals(\sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn()), $route->getFqn());
        $this->assertInstanceOf(PropertyPoint::class, $route->getSourcePoint());
        $this->assertInstanceOf(PropertyPoint::class, $route->getTargetPoint());

        $targetPoint = new ParameterPoint(\sprintf('%s::privateMethodToTest()::$parameter', ParameterPointTest::class));
        $route = new Route($sourcePoint, $targetPoint);

        $this->assertEquals(\sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn()), $route->getFqn());
        $this->assertInstanceOf(PropertyPoint::class, $route->getSourcePoint());
        $this->assertInstanceOf(ParameterPoint::class, $route->getTargetPoint());

        $sourcePoint = new MethodPoint(\sprintf('%s::privateMethodToTest()', MethodPointTest::class));
        $targetPoint = new PropertyPoint(\sprintf('%s::$privatePropertyToTest', PropertyPointTest::class));
        $route = new Route($sourcePoint, $targetPoint);

        $this->assertEquals(\sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn()), $route->getFqn());
        $this->assertInstanceOf(MethodPoint::class, $route->getSourcePoint());
        $this->assertInstanceOf(PropertyPoint::class, $route->getTargetPoint());

        $targetPoint = new ParameterPoint(\sprintf('%s::privateMethodToTest()::$parameter', ParameterPointTest::class));
        $route = new Route($sourcePoint, $targetPoint);

        $this->assertEquals(\sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn()), $route->getFqn());
        $this->assertInstanceOf(MethodPoint::class, $route->getSourcePoint());
        $this->assertInstanceOf(ParameterPoint::class, $route->getTargetPoint());
    }

    public function testRouteConstructionSourcePointException(): void
    {
        $sourcePoint = new ParameterPoint(\sprintf('%s::privateMethodToTest()::$parameter', ParameterPointTest::class));
        $targetPoint = new PropertyPoint(\sprintf('%s::$privatePropertyToTest', PropertyPointTest::class));

        $this->expectException(InvalidArgumentException::class);
        new Route($sourcePoint, $targetPoint);
    }

    public function testRouteConstructionTargetPointException(): void
    {
        $sourcePoint = new PropertyPoint(\sprintf('%s::$privatePropertyToTest', PropertyPointTest::class));
        $targetPoint = new MethodPoint(\sprintf('%s::privateMethodToTest()', MethodPointTest::class));

        $this->expectException(InvalidArgumentException::class);
        new Route($sourcePoint, $targetPoint);
    }
}
