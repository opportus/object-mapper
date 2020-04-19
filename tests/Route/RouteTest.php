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
use Opportus\ObjectMapper\Point\AbstractPoint;
use Opportus\ObjectMapper\Point\CheckPointCollection;
use Opportus\ObjectMapper\Point\MethodPoint;
use Opportus\ObjectMapper\Point\ParameterPoint;
use Opportus\ObjectMapper\Point\PropertyPoint;
use Opportus\ObjectMapper\Route\Route;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The route test.
 *
 * @package Opportus\ObjectMapper\Tests\Route
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidPoints
     * @param AbstractPoint $sourcePoint
     * @param AbstractPoint $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testConstructException(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $this->expectException(InvalidArgumentException::class);
        new Route($sourcePoint, $targetPoint, $checkPoints);
    }

    /**
     * @dataProvider providePoints
     * @param AbstractPoint $sourcePoint
     * @param AbstractPoint $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testConstruct(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertInstanceOf(Route::class, $route);
    }

    /**
     * @dataProvider providePoints
     * @param AbstractPoint $sourcePoint
     * @param AbstractPoint $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetFqn(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertSame(
            \sprintf('%s:%s', $sourcePoint->getFqn(), $targetPoint->getFqn()),
            $route->getFqn()
        );
    }

    /**
     * @dataProvider providePoints
     * @param AbstractPoint $sourcePoint
     * @param AbstractPoint $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetSourcePoint(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
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
     * @param AbstractPoint $sourcePoint
     * @param AbstractPoint $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetTargetPoint(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
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
     * @param AbstractPoint $sourcePoint
     * @param AbstractPoint $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetCheckPoints(
        AbstractPoint $sourcePoint,
        AbstractPoint $targetPoint,
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
                    PropertyPoint::class,
                    'Class.$property'
                ),
                $this->buildPoint(
                    PropertyPoint::class,
                    'Class.$property'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    PropertyPoint::class,
                    'Class.$property'
                ),
                $this->buildPoint(
                    ParameterPoint::class,
                    'Class.method().$parameter'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodPoint::class,
                    'Class.method()'
                ),
                $this->buildPoint(
                    PropertyPoint::class,
                    'Class.$property'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodPoint::class,
                    'Class.method()'
                ),
                $this->buildPoint(
                    ParameterPoint::class,
                    'Class.method().$parameter'
                ),
                new CheckPointCollection(),
            ],
        ];
    }

    /**
     * @return array|array[]
     */
    public function provideInvalidPoints(): array
    {
        return [
            [
                $this->buildPoint(
                    ParameterPoint::class,
                    'Class.method().$parameter'
                ),
                $this->buildPoint(
                    ParameterPoint::class,
                    'Class.method().$parameter'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    ParameterPoint::class,
                    'Class.method().$parameter'
                ),
                $this->buildPoint(
                    PropertyPoint::class,
                    'Class.$property'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    PropertyPoint::class,
                    'Class.$property'
                ),
                $this->buildPoint(
                    MethodPoint::class,
                    'Class.method()'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodPoint::class,
                    'Class.method()'
                ),
                $this->buildPoint(
                    MethodPoint::class,
                    'Class.method()'
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
