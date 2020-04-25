<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Point\MethodObjectPoint;
use Opportus\ObjectMapper\Point\ParameterObjectPoint;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Point\PointFactoryInterface;
use Opportus\ObjectMapper\Point\PropertyObjectPoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The point factory test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactoryTest extends FinalBypassTestCase
{
    public function testConstruct(): void
    {
        $pointFactory = new PointFactory();

        static::assertInstanceOf(PointFactory::class, $pointFactory);
        static::assertInstanceOf(PointFactoryInterface::class, $pointFactory);
    }

    public function testCreatePoint(): void
    {
        $pointFactory = new PointFactory();

        $pointFqns =  [
            PropertyObjectPoint::class => \sprintf(
                '%s.$%s',
                PointFactoryTestClass::class,
                'property'
            ),
            MethodObjectPoint::class => \sprintf(
                '%s.%s()',
                PointFactoryTestClass::class,
                'method'
            ),
            ParameterObjectPoint::class => \sprintf(
                '%s.%s().$%s',
                PointFactoryTestClass::class,
                'method',
                'parameter'
            ),
        ];


        foreach ($pointFqns as $pointType => $pointFqn) {
            $point = $pointFactory->createObjectPoint($pointFqn);

            static::assertInstanceOf($pointType, $point);
            static::assertSame($pointFqn, $point->getFqn());
        }

        $this->expectException(InvalidArgumentException::class);
        $pointFactory->createObjectPoint('test');
    }
}

/**
 * The point factory test class.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactoryTestClass
{
    public $property;

    /**
     * @param null $parameter
     */
    public function method($parameter = null)
    {
    }
}
