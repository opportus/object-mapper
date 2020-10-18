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
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\MethodDynamicSourcePoint;
use Opportus\ObjectMapper\Point\MethodParameterDynamicTargetPoint;
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyDynamicSourcePoint;
use Opportus\ObjectMapper\Point\PropertyDynamicTargetPoint;
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\Tests\ObjectA;
use Opportus\ObjectMapper\Tests\ObjectB;
use PHPUnit\Framework\TestCase;

/**
 * The route test.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteTest extends TestCase
{
    /**
     * @dataProvider provideConstructArguments
     */
    public function testConstruct(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertInstanceOf(RouteInterface::class, $route);
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testGetFqn(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertSame(
            \sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn()),
            $route->getFqn()
        );
    }

    /**
     * @dataProvider provideConstructArguments
     */
    public function testGetSourcePoint(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

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
     * @dataProvider provideConstructArguments
     */
    public function testGetTargetPoint(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

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
     * @dataProvider provideConstructArguments
     */
    public function testGetCheckPoints(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertCount(\count($checkPoints), $route->getCheckPoints());
        static::assertSame($checkPoints, $route->getCheckPoints());
    }

    public function provideConstructArguments(): array
    {
        return [
            [
                new PropertyStaticSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyStaticSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyStaticSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyStaticSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setZ',
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],

            [
                new MethodStaticSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodStaticSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodStaticSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodStaticSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setZ',
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],

            [
                new PropertyDynamicSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyDynamicSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyDynamicSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyDynamicSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setZ',
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],

            [
                new MethodDynamicSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodDynamicSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodDynamicSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodDynamicSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setZ',
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
        ];
    }
}
