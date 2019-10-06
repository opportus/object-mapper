<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
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
use PHPUnit\Framework\TestCase;

/**
 * The no path finding strategy test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class NoPathFindingStrategyTest extends TestCase
{
    private $strategy = null;

    /**
     * @dataProvider provideSourceAndTargetClassFqn
     */
    public function testGetRoutes(string $sourceClassFqn, string $targetClassFqn): void
    {
        $this->strategy = $this->strategy ?? $this->buildStrategy();

        $context = $this->buildContext($sourceClassFqn, $targetClassFqn);

        foreach ($this->strategy->getRoutes($context) as $route) {
            $this->assertEquals($context->getSourceClassFqn(), $route->getSourcePoint()->getClassFqn());
            $this->assertEquals($context->getTargetClassFqn(), $route->getTargetPoint()->getClassFqn());
        }

        if (\count($this->strategy->getRoutes($context)) === 0) {
            $this->assertEquals('TestSourceClass5', $context->getSourceClassFqn());
            $this->assertEquals('TestTargetClass5', $context->getTargetClassFqn());
        }
    }

    public function provideSourceAndTargetClassFqn(): array
    {
        $sourceAndTargetClassFqn = [];

        for ($i = 1; $i <= 5; $i++) {
            $sourceAndTargetClassFqn[$i] = [
                \sprintf('TestSourceClass%d', $i),
                \sprintf('TestTargetClass%d', $i),
            ];
        }

        return $sourceAndTargetClassFqn;
    }

    private function buildStrategy(): NoPathFindingStrategy
    {
        $routes = [];

        for ($i = 0; $i < 5; $i++) {
            $sourcePoint = $this->getMockBuilder(PropertyPoint::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $sourcePoint
                ->method('getClassFqn')
                ->willReturn(\sprintf('TestSourceClass%d', $i))
            ;

            $targetPoint = $this->getMockBuilder(PropertyPoint::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $targetPoint
                ->method('getClassFqn')
                ->willReturn(\sprintf('TestTargetClass%d', $i))
            ;

            $route = $this->getMockBuilder(Route::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
            $route
                ->method('getFqn')
                ->willReturn(\sprintf('route_%d', $i))
            ;
            $route
                ->method('getSourcePoint')
                ->willReturn($sourcePoint)
            ;
            $route
                ->method('getTargetPoint')
                ->willReturn($targetPoint)
            ;

            $routes[$i] = $route;
        }

        return new NoPathFindingStrategy(new RouteCollection($routes));
    }

    private function buildContext(string $sourceClassFqn, string $targetClassFqn): Context
    {
        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $context
            ->method('getSourceClassFqn')
            ->willReturn($sourceClassFqn)
        ;
        $context
            ->method('getTargetClassFqn')
            ->willReturn($targetClassFqn)
        ;

        return $context;
    }
}
