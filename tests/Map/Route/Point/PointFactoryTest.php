<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Map\Route\Point;

use Opportus\ObjectMapper\Exception\InvalidPointException;
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use PHPUnit\Framework\TestCase;

/**
 * The point factory test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactoryTest extends TestCase
{
    public function testMethodPointCreation(): void
    {
        $pointFactory = new PointFactory();
        $methodPointTest = new MethodPointTest();

        foreach ($methodPointTest->getMethodsToTest() as $methodName) {
            $pointFqn = \sprintf('%s.%s()', MethodPointTest::class, $methodName);

            $this->assertInstanceOf(MethodPoint::class, $pointFactory->createPoint($pointFqn));
        }
    }

    public function testParameterPointCreation(): void
    {
        $pointFactory = new PointFactory();
        $parameterPointTest = new ParameterPointTest();

        foreach ($parameterPointTest->getParametersToTest() as $methodName => $parameterName) {
            $pointFqn = \sprintf('%s.%s().$%s', ParameterPointTest::class, $methodName, $parameterName);

            $this->assertInstanceOf(ParameterPoint::class, $pointFactory->createPoint($pointFqn));
        }
    }

    public function testPropertyPointCreation(): void
    {
        $pointFactory = new PointFactory();
        $propertyPointTest = new PropertyPointTest();

        foreach ($propertyPointTest->getPropertiesToTest() as $propertyName) {
            $pointFqn = \sprintf('%s.$%s', PropertyPointTest::class, $propertyName);

            $this->assertInstanceOf(PropertyPoint::class, $pointFactory->createPoint($pointFqn));
        }
    }

    public function testInvalidPointCreation(): void
    {
        $pointFactory = new PointFactory();

        $this->expectException(InvalidPointException::class);

        $pointFactory->createPoint('test');
    }
}
