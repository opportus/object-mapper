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
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;
use Opportus\ObjectMapper\Tests\Map\Route\Point\MethodPointTest;
use Opportus\ObjectMapper\Tests\Map\Route\Point\ParameterPointTest;
use Opportus\ObjectMapper\Tests\Map\Route\Point\PropertyPointTest;
use PHPUnit\Framework\TestCase;

/**
 * The route builder test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteBuilderTest extends TestCase
{
    public function testRouteBuilding(): void
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        $cases = [
            [
                \sprintf('%s.$privatePropertyToTest', PropertyPointTest::class),
                \sprintf('%s.$privatePropertyToTest', PropertyPointTest::class),
            ],
            [
                \sprintf('%s.$privatePropertyToTest', PropertyPointTest::class),
                \sprintf('%s.privateMethodToTest().$parameter', ParameterPointTest::class),
            ],
            [
                \sprintf('%s.privateMethodToTest()', MethodPointTest::class),
                \sprintf('%s.$privatePropertyToTest', PropertyPointTest::class),
            ],
            [
                \sprintf('%s.privateMethodToTest()', MethodPointTest::class),
                \sprintf('%s.privateMethodToTest().$parameter', ParameterPointTest::class),
            ],
        ];

        foreach ($cases as $arguments) {
            $sourcePointFqn = $arguments[0];
            $targetPointFqn = $arguments[1];

            $route = $routeBuilder->buildRoute($sourcePointFqn, $targetPointFqn);
            
            $this->assertInstanceOf(Route::class, $route);
            $this->assertEquals(\sprintf('%s:%s', $sourcePointFqn, $targetPointFqn), $route->getFqn());
        }
    }

    public function testRouteBuildingSourcePointException(): void
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        $sourcePointFqn = \sprintf('%s.privateMethodToTest().$parameter', ParameterPointTest::class);
        $targetPointFqn = \sprintf('%s.$privatePropertyToTest', PropertyPointTest::class);

        $this->expectException(InvalidArgumentException::class);
        $routeBuilder->buildRoute($sourcePointFqn, $targetPointFqn);
    }

    public function testRouteBuildingTargetPointException(): void
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        $sourcePointFqn = \sprintf('%s.$privatePropertyToTest', PropertyPointTest::class);
        $targetPointFqn = \sprintf('%s.privateMethodToTest()', MethodPointTest::class);

        $this->expectException(InvalidArgumentException::class);
        $routeBuilder->buildRoute($sourcePointFqn, $targetPointFqn);
    }
}
