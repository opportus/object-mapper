<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Map\Filter;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Filter\Filter;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\ObjectMapper;
use PHPUnit\Framework\TestCase;

/**
 * The filter test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Filter
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class FilterTest extends TestCase
{
    public function testGetRouteFqn(): void
    {
        $filter = new Filter($this->buildRoute(), function () {
        });

        $this->assertEquals('route_1', $filter->getRouteFqn());
    }

    public function testGetValue(): void
    {
        $objectMapper = $this->buildObjectMapper();
        $route = $this->buildRoute();
        $callable = function ($route, $context, $objectMapper) {
            return
                $route instanceof Route &&
                $context instanceof Context &&
                $objectMapper instanceof ObjectMapper
            ;
        };

        $filter = new Filter($route, $callable);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $context
            ->method('getSourceClassFqn')
            ->willReturn('TestSourceClass')
        ;
        $context
            ->method('getTargetClassFqn')
            ->willReturn('TestTargetClass')
        ;

        $this->assertTrue($filter->getValue($context, $objectMapper));
    }

    public function testGetValueInvalidSourceClassException(): void
    {
        $objectMapper = $this->buildObjectMapper();
        $route = $this->buildRoute();
        $callable = function ($route, $context, $objectMapper) {
        };

        $filter = new Filter($route, $callable);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $context
            ->method('getSourceClassFqn')
            ->willReturn('invalid_source_class')
        ;
        $context
            ->method('getTargetClassFqn')
            ->willReturn('TestTargetClass')
        ;

        $this->expectException(InvalidOperationException::class);

        $filter->getValue($context, $objectMapper);
    }

    public function testGetValueInvalidTargetClassException(): void
    {
        $objectMapper = $this->buildObjectMapper();
        $route = $this->buildRoute();
        $callable = function ($route, $context, $objectMapper) {
        };

        $filter = new Filter($route, $callable);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $context
            ->method('getSourceClassFqn')
            ->willReturn('TestSourceClass')
        ;
        $context
            ->method('getTargetClassFqn')
            ->willReturn('invalid_source_class')
        ;

        $this->expectException(InvalidOperationException::class);

        $filter->getValue($context, $objectMapper);
    }

    private function buildRoute(): Route
    {
        $sourcePoint = $this->getMockBuilder(PropertyPoint::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $sourcePoint
            ->method('getClassFqn')
            ->willReturn('TestSourceClass')
        ;

        $targetPoint = $this->getMockBuilder(PropertyPoint::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $targetPoint
            ->method('getClassFqn')
            ->willReturn('TestTargetClass')
        ;

        $route = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $route
            ->method('getFqn')
            ->willReturn('route_1')
        ;
        $route
            ->method('getSourcePoint')
            ->willReturn($sourcePoint)
        ;
        $route
            ->method('getTargetPoint')
            ->willReturn($targetPoint)
        ;

        return $route;
    }

    private function buildObjectMapper(): ObjectMapper
    {
        return $this->getMockBuilder(ObjectMapper::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
