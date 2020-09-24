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
use Opportus\ObjectMapper\Point\MethodObjectPoint;
use Opportus\ObjectMapper\Point\ObjectPoint;
use Opportus\ObjectMapper\Point\MethodParameterObjectPoint;
use Opportus\ObjectMapper\Point\PropertyObjectPoint;
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
     * @param ObjectPoint $sourcePoint
     * @param ObjectPoint $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testConstructException(
        ObjectPoint $sourcePoint,
        ObjectPoint $targetPoint,
        CheckPointCollection $checkPoints
    ): void {
        $this->expectException(InvalidArgumentException::class);
        new Route($sourcePoint, $targetPoint, $checkPoints);
    }

    /**
     * @dataProvider providePoints
     * @param ObjectPoint $sourcePoint
     * @param ObjectPoint $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testConstruct(
        ObjectPoint $sourcePoint,
        ObjectPoint $targetPoint,
        ?CheckPointCollection $checkPoints
    ): void {
        $route = new Route($sourcePoint, $targetPoint, $checkPoints);

        static::assertInstanceOf(Route::class, $route);
    }

    /**
     * @dataProvider providePoints
     * @param ObjectPoint $sourcePoint
     * @param ObjectPoint $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetFqn(
        ObjectPoint $sourcePoint,
        ObjectPoint $targetPoint,
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
     * @param ObjectPoint $sourcePoint
     * @param ObjectPoint $targetPoint
     * @param null|CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetSourcePoint(
        ObjectPoint $sourcePoint,
        ObjectPoint $targetPoint,
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
     * @param ObjectPoint $sourcePoint
     * @param ObjectPoint $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetTargetPoint(
        ObjectPoint $sourcePoint,
        ObjectPoint $targetPoint,
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
     * @param ObjectPoint $sourcePoint
     * @param ObjectPoint $targetPoint
     * @param CheckPointCollection $checkPoints
     * @throws InvalidArgumentException
     */
    public function testGetCheckPoints(
        ObjectPoint $sourcePoint,
        ObjectPoint $targetPoint,
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
                    PropertyObjectPoint::class,
                    'Class.$property'
                ),
                $this->buildPoint(
                    PropertyObjectPoint::class,
                    'Class.$property'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    PropertyObjectPoint::class,
                    'Class.$property'
                ),
                $this->buildPoint(
                    MethodParameterObjectPoint::class,
                    'Class.method().$parameter'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodObjectPoint::class,
                    'Class.method()'
                ),
                $this->buildPoint(
                    PropertyObjectPoint::class,
                    'Class.$property'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodObjectPoint::class,
                    'Class.method()'
                ),
                $this->buildPoint(
                    MethodParameterObjectPoint::class,
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
                    MethodParameterObjectPoint::class,
                    'Class.method().$parameter'
                ),
                $this->buildPoint(
                    MethodParameterObjectPoint::class,
                    'Class.method().$parameter'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodParameterObjectPoint::class,
                    'Class.method().$parameter'
                ),
                $this->buildPoint(
                    PropertyObjectPoint::class,
                    'Class.$property'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    PropertyObjectPoint::class,
                    'Class.$property'
                ),
                $this->buildPoint(
                    MethodObjectPoint::class,
                    'Class.method()'
                ),
                new CheckPointCollection(),
            ],
            [
                $this->buildPoint(
                    MethodObjectPoint::class,
                    'Class.method()'
                ),
                $this->buildPoint(
                    MethodObjectPoint::class,
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
