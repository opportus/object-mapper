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
use Opportus\ObjectMapper\Point\PropertyObjectPoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The property object point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyObjectPointTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidPropertyPointFqns
     * @param $invalidPropertyPointFqn
     * @throws InvalidArgumentException
     */
    public function testConstructException($invalidPropertyPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PropertyObjectPoint($invalidPropertyPointFqn);
    }

    /**
     * @dataProvider providePropertyPointFqnTokens
     * @param string $className
     * @param string $propertyName
     * @throws InvalidArgumentException
     */
    public function testConstruct(string $className, string $propertyName): void
    {
        static::assertInstanceOf(
            PropertyObjectPoint::class,
            new PropertyObjectPoint(\sprintf('%s.$%s', $className, $propertyName))
        );
    }

    /**
     * @dataProvider providePropertyPointFqnTokens
     * @param string $className
     * @param string $propertyName
     * @throws InvalidArgumentException
     */
    public function testGetFqn(string $className, string $propertyName): void
    {
        $propertyPoint = $this->buildPropertyPoint($className, $propertyName);

        static::assertSame(
            \sprintf('%s.$%s', $className, $propertyName),
            $propertyPoint->getFqn()
        );
    }

    /**
     * @dataProvider providePropertyPointFqnTokens
     * @param string $className
     * @param string $propertyName
     * @throws InvalidArgumentException
     */
    public function testGetClassFqn(
        string $className,
        string $propertyName
    ): void {
        $propertyPoint = $this->buildPropertyPoint($className, $propertyName);

        static::assertSame($className, $propertyPoint->getClassFqn());
    }

    /**
     * @dataProvider providePropertyPointFqnTokens
     * @param string $className
     * @param string $propertyName
     * @throws InvalidArgumentException
     */
    public function testGetName(string $className, string $propertyName): void
    {
        $propertyPoint = $this->buildPropertyPoint($className, $propertyName);

        static::assertSame($propertyName, $propertyPoint->getName());
    }

    /**
     * @return array|string[][]
     */
    public function providePropertyPointFqnTokens(): array
    {
        return [
            [PropertyObjectPointTestClass::class, 'privateProperty'],
            [PropertyObjectPointTestClass::class, 'protectedProperty'],
            [PropertyObjectPointTestClass::class, 'publicProperty'],
        ];
    }

    /**
     * @return array|array[]
     */
    public function provideInvalidPropertyPointFqns(): array
    {
        return [
            // Invalid syntax...
            [\sprintf(
                '%s.%s',
                PropertyObjectPointTestClass::class,
                'publicProperty'
            )],
            [\sprintf(
                '%s$%s',
                PropertyObjectPointTestClass::class,
                'publicProperty'
            )],
            [\sprintf(
                '%s.$',
                PropertyObjectPointTestClass::class
            )],

            // Invalid reflection...
            [\sprintf(
                '%s.$%s',
                'InvalidClass',
                'publicProperty'
            )],
            [\sprintf(
                '%s.$%s',
                PropertyObjectPointTestClass::class,
                'invalidProperty'
            )],
        ];
    }

    /**
     * @param string $className
     * @param string $propertyName
     * @return PropertyObjectPoint
     * @throws InvalidArgumentException
     */
    private function buildPropertyPoint(
        string $className,
        string $propertyName
    ): PropertyObjectPoint {
        $propertyPointFqn = \sprintf('%s.$%s', $className, $propertyName);

        return new PropertyObjectPoint($propertyPointFqn);
    }
}

/**
 * The property object point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyObjectPointTestClass
{
    private $privateProperty = 1;
    protected $protectedProperty = 1;
    public $publicProperty = 1;
}
