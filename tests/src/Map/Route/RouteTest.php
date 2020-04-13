<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Point\AbstractPoint;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The route test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidPoints
     */
    public function testConstructException(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint
    ): void {
        $this->expectException(InvalidArgumentException::class);
        new Route($sourcePoint, $targetPoint);
    }

    /**
     * @dataProvider providePoints
     */
    public function testConstruct(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        $this->assertInstanceOf(Route::class, $route);
    }

    /**
     * @dataProvider providePoints
     */
    public function testGetFqn(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        $this->assertSame(\sprintf('%s:%s', $sourcePoint->getFqn(), $targetPoint->getFqn()), $route->getFqn());
    }

    /**
     * @dataProvider providePoints
     */
    public function testGetSourcePoint(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        $this->assertInstanceOf(\get_class($sourcePoint), $route->getSourcePoint());
    }

    /**
     * @dataProvider providePoints
     */
    public function testGetTargetPoint(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        $this->assertInstanceOf(\get_class($targetPoint), $route->getTargetPoint());
    }

    /**
     * @dataProvider providePoints
     */
    public function testGetCheckPoints(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        if (null === $checkPoints) {
            $this->assertInstanceOf(CheckPointCollection::class, $route->getCheckPoints());
            $this->assertCount(0, $route->getCheckPoints());
        } else {
            $this->assertSame($checkPoints, $route->getCheckPoints());
        }
    }

    public function providePoints(): array
    {
        return [
            [
                $this->buildPoint(PropertyPoint::class, 'Class.$property'),
                $this->buildPoint(PropertyPoint::class, 'Class.$property'),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(PropertyPoint::class, 'Class.$property'),
                $this->buildPoint(ParameterPoint::class, 'Class.method().$parameter'),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(MethodPoint::class, 'Class.method()'),
                $this->buildPoint(PropertyPoint::class, 'Class.$property'),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(MethodPoint::class, 'Class.method()'),
                $this->buildPoint(ParameterPoint::class, 'Class.method().$parameter'),
                new CheckPointCollection(),
            ],
        ];
    }

    public function provideInvalidPoints(): array
    {
        return [
            [
                $this->buildPoint(ParameterPoint::class, 'Class.method().$parameter'),
                $this->buildPoint(ParameterPoint::class, 'Class.method().$parameter'),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(ParameterPoint::class, 'Class.method().$parameter'),
                $this->buildPoint(PropertyPoint::class, 'Class.$property'),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(PropertyPoint::class, 'Class.$property'),
                $this->buildPoint(MethodPoint::class, 'Class.method()'),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(MethodPoint::class, 'Class.method()'),
                $this->buildPoint(MethodPoint::class, 'Class.method()'),
                new CheckPointCollection(),
            ],
        ];
    }

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
