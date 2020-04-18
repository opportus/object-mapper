<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Map\Route\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The property point test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyPointTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidPropertyPointFqns
     * @param $invalidPropertyPointFqn
     * @throws InvalidArgumentException
     */
    public function testConstructException($invalidPropertyPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PropertyPoint($invalidPropertyPointFqn);
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
            PropertyPoint::class,
            new PropertyPoint(\sprintf('%s.$%s', $className, $propertyName))
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
            [PropertyPointTestClass::class, 'privateProperty'],
            [PropertyPointTestClass::class, 'protectedProperty'],
            [PropertyPointTestClass::class, 'publicProperty'],
        ];
    }

    /**
     * @return array|array[]
     */
    public function provideInvalidPropertyPointFqns(): array
    {
        return [
            [\sprintf(
                '%s.%s',
                PropertyPointTestClass::class,
                'publicProperty'
            )],
            [\sprintf(
                '%s$%s',
                PropertyPointTestClass::class,
                'publicProperty'
            )],
            [\sprintf(
                '%s.$',
                PropertyPointTestClass::class
            )],
        ];
    }

    /**
     * @param string $className
     * @param string $propertyName
     * @return PropertyPoint
     * @throws InvalidArgumentException
     */
    private function buildPropertyPoint(
        string $className,
        string $propertyName
    ): PropertyPoint {
        $propertyPointFqn = \sprintf('%s.$%s', $className, $propertyName);

        return new PropertyPoint($propertyPointFqn);
    }
}

/**
 * The property point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyPointTestClass
{
    private $privateProperty = 1;
    protected $protectedProperty = 1;
    public $publicProperty = 1;
}
