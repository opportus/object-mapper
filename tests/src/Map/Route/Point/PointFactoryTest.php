<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Route\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\Point\PointFactoryInterface;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The point factory test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactoryTest extends FinalBypassTestCase
{
    public function testConstruct(): void
    {
        $pointFactory = new PointFactory();

        $this->assertInstanceOf(PointFactory::class, $pointFactory);
        $this->assertInstanceOf(PointFactoryInterface::class, $pointFactory);
    }

    public function testCreatePoint(): void
    {
        $pointFactory = new PointFactory();

        $pointFqns =  [
            PropertyPoint::class  =>  \sprintf('%s.$%s', PointFactoryTestClass::class, 'property'),
            MethodPoint::class    =>    \sprintf('%s.%s()', PointFactoryTestClass::class, 'method'),
            ParameterPoint::class => \sprintf('%s.%s().$%s', PointFactoryTestClass::class, 'method', 'parameter'),
        ];


        foreach ($pointFqns as $pointType => $pointFqn) {
            $point = $pointFactory->createPoint($pointFqn);

            $this->assertInstanceOf($pointType, $point);
            $this->assertSame($pointFqn, $point->getFqn());
        }

        $this->expectException(InvalidArgumentException::class);
        $pointFactory->createPoint('test');
    }
}

/**
 * The point factory test class.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactoryTestClass
{
    public $property;

    public function method($parameter = null)
    {
    }
}
