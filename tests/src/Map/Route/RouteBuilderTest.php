<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;
use Opportus\ObjectMapper\Map\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;
use TypeError;

/**
 * The route builder test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteBuilderTest extends FinalBypassTestCase
{
    public function testConstructException()
    {
        $this->expectException(TypeError::class);
        new RouteBuilder(new RouteBuilderTestClass());
    }

    public function testConstruct()
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        $this->assertInstanceOf(RouteBuilder::class, $routeBuilder);
        $this->assertInstanceOf(RouteBuilderInterface::class, $routeBuilder);
    }

    /**
     * @dataProvider providePointFqns
     */
    public function testBuildRoute(string $sourcePointFqn, string $targetPointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        $route = $routeBuilder->buildRoute($sourcePointFqn, $targetPointFqn);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame($sourcePointFqn, $route->getSourcePoint()->getFqn());
        $this->assertSame($targetPointFqn, $route->getTargetPoint()->getFqn());
    }

    /**
     * @dataProvider provideInvalidPointFqns
     */
    public function testBuildRouteException(string $sourcePointFqn, string $targetPointFqn): void
    {
        $routeBuilder = $this->buildRouteBuilder();

        $this->expectException(InvalidArgumentException::class);
        $routeBuilder->buildRoute($sourcePointFqn, $targetPointFqn);
    }

    public function providePointFqns(): array
    {
        return [
            [
                \sprintf('%s.$property', RouteBuilderTestClass::class),
                \sprintf('%s.$property', RouteBuilderTestClass::class),
            ],
            [
                \sprintf('%s.$property', RouteBuilderTestClass::class),
                \sprintf('%s.method().$parameter', RouteBuilderTestClass::class),
            ],
            [
                \sprintf('%s.method()', RouteBuilderTestClass::class),
                \sprintf('%s.$property', RouteBuilderTestClass::class),
            ],
            [
                \sprintf('%s.method()', RouteBuilderTestClass::class),
                \sprintf('%s.method().$parameter', RouteBuilderTestClass::class),
            ],
        ];
    }

    public function provideInvalidPointFqns(): array
    {
        return [
            [
                \sprintf('%s.method().$parameter', RouteBuilderTestClass::class),
                \sprintf('%s.$property', RouteBuilderTestClass::class),
            ],
            [
                \sprintf('%s.method().$parameter', RouteBuilderTestClass::class),
                \sprintf('%s.method().$parameter', RouteBuilderTestClass::class),
            ],
            [
                \sprintf('%s.$property', RouteBuilderTestClass::class),
                \sprintf('%s.method()', RouteBuilderTestClass::class),
            ],
            [
                \sprintf('%s.method()', RouteBuilderTestClass::class),
                \sprintf('%s.method()', RouteBuilderTestClass::class),
            ],
        ];
    }

    private function buildRouteBuilder(): RouteBuilder
    {
        return new RouteBuilder(new PointFactory());
    }
}

/**
 * The route builder test class.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route
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
