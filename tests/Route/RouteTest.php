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
use Opportus\ObjectMapper\Point\CheckPointCollection;
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Route\RouteInterface;
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
     * @dataProvider providePoints
     * @param SourcePointInterface $sourcePoint
     * @param TargetPointInterface $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testConstruct(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertInstanceOf(RouteInterface::class, $route);
    }

    /**
     * @dataProvider providePoints
     * @param SourcePointInterface $sourcePoint
     * @param TargetPointInterface $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetFqn(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertSame(
            \sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn()),
            $route->getFqn()
        );
    }

    /**
     * @dataProvider providePoints
     * @param SourcePointInterface $sourcePoint
     * @param TargetPointInterface $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetSourcePoint(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertInstanceOf(
            \get_class($sourcePoint),
            $route->getSourcePoint()
        );
    }

    /**
     * @dataProvider providePoints
     * @param SourcePointInterface $sourcePoint
     * @param TargetPointInterface $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
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
    }

    /**
     * @dataProvider providePoints
     * @param SourcePointInterface $sourcePoint
     * @param TargetPointInterface $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetCheckPoints(
        SourcePointInterface $sourcePoint,
        TargetPointInterface $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        if (null === $checkPoints) {
            static::assertInstanceOf(
                CheckPointCollection::class,
                $route->getCheckPoints()
            );

            static::assertCount(0, $route->getCheckPoints());
        } else {
            static::assertSame($checkPoints, $route->getCheckPoints());
        }
    }

    /**
     * @return array|array[]
     */
    public function providePoints(): array
    {
        return [
            [
                $this->buildPoint(
                    PropertyStaticSourcePoint::class,
                    'Class::$property'
                ),
                $this->buildPoint(
                    PropertyStaticTargetPoint::class,
                    'Class::$property'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    PropertyStaticSourcePoint::class,
                    'Class::$property'
                ),
                $this->buildPoint(
                    MethodParameterStaticTargetPoint::class,
                    'Class::method()::$parameter'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodStaticSourcePoint::class,
                    'Class::method()'
                ),
                $this->buildPoint(
                    PropertyStaticTargetPoint::class,
                    'Class::$property'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodStaticSourcePoint::class,
                    'Class::method()'
                ),
                $this->buildPoint(
                    MethodParameterStaticTargetPoint::class,
                    'Class::method()::$parameter'
                ),
                new CheckPointCollection(),
            ],
        ];
    }

    /**
     * @param string $pointType
     * @param string $pointFqn
     * @return object
     */
    private function buildPoint(string $pointType, string $pointFqn): object
    {
        $point = $this->getMockBuilder($pointType)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $point
            ->method('getFqn')
            ->willReturn($pointFqn)
        ;

        return $point;
    }
}
