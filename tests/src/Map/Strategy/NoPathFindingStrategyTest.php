<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Strategy;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Strategy\NoPathFindingStrategy;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;
use TypeError;

/**
 * The no path finding strategy test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Strategy
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class NoPathFindingStrategyTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideRouteCollections
     */
    public function testConstruct(RouteCollection $routeCollection): void
    {
        $strategy = new NoPathFindingStrategy($routeCollection);

        $this->assertInstanceOf(NoPathFindingStrategy::class, $strategy);
        $this->assertInstanceOf(PathFindingStrategyInterface::class, $strategy);
    }

    public function testConstructException(): void
    {
        $this->expectException(TypeError::class);
        new NoPathFindingStrategy(new \StdClass());
    }

    /**
     * @dataProvider provideRouteCollections
     */
    public function testGetRoutes(RouteCollection $routeCollection): void
    {
        $strategy = new NoPathFindingStrategy($routeCollection);
        $context = $this->buildContext();

        $routes = $strategy->getRoutes($context);

        $this->assertInstanceOf(RouteCollection::class, $routes);
        $this->assertSame(1, \count($routes));
        $this->assertArrayHasKey('Class1.$property:Class1.$property', $routes);
    }

    public function provideRouteCollections(): array
    {
        $routes = [];
        for ($i = 0; $i < 3; $i++) {
            $sourcePoint = $this->getMockBuilder(PropertyPoint::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $sourcePoint
                ->method('getFqn')
                ->willReturn(\sprintf('Class%d.$property', $i))
            ;
            $sourcePoint
                ->method('getClassFqn')
                ->willReturn(\sprintf('Class%d', $i))
            ;

            $targetPoint = $this->getMockBuilder(PropertyPoint::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $targetPoint
                ->method('getFqn')
                ->willReturn(\sprintf('Class%d.$property', $i))
            ;
            $targetPoint
                ->method('getClassFqn')
                ->willReturn(\sprintf('Class%d', $i))
            ;

            $route = new Route($sourcePoint, $targetPoint);

            $routes[$route->getFqn()] = $route;
        }

        return [[new RouteCollection($routes)]];
    }

    private function buildContext(): Context
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $context
            ->method('getSourceClassFqn')
            ->willReturn('Class1')
        ;
        $context
            ->method('getTargetClassFqn')
            ->willReturn('Class1')
        ;

        return $context;
    }
}
