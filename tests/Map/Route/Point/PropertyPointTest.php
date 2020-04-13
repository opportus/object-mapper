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
     */
    public function testConstructException($invalidPropertyPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PropertyPoint($invalidPropertyPointFqn);
    }

    /**
     * @dataProvider providePropertyPointFqnTokens
     */
    public function testConstruct(string $className, string $propertyName): void
    {
        $this->assertInstanceOf(PropertyPoint::class, new PropertyPoint(\sprintf('%s.$%s', $className, $propertyName)));
    }

    /**
     * @dataProvider providePropertyPointFqnTokens
     */
    public function testGetFqn(string $className, string $propertyName): void
    {
        $propertyPoint = $this->buildPropertyPoint($className, $propertyName);

        $this->assertSame(\sprintf('%s.$%s', $className, $propertyName), $propertyPoint->getFqn());
    }

    /**
     * @dataProvider providePropertyPointFqnTokens
     */
    public function testGetClassFqn(string $className, string $propertyName): void
    {
        $propertyPoint = $this->buildPropertyPoint($className, $propertyName);

        $this->assertSame($className, $propertyPoint->getClassFqn());
    }

    /**
     * @dataProvider providePropertyPointFqnTokens
     */
    public function testGetName(string $className, string $propertyName): void
    {
        $propertyPoint = $this->buildPropertyPoint($className, $propertyName);

        $this->assertSame($propertyName, $propertyPoint->getName());
    }

    public function providePropertyPointFqnTokens(): array
    {
        return [
            [PropertyPointTestClass::class, 'privateProperty'],
            [PropertyPointTestClass::class, 'protectedProperty'],
            [PropertyPointTestClass::class, 'publicProperty'],
        ];
    }

    public function provideInvalidPropertyPointFqns(): array
    {
        return [
            // Invalid syntax...
            [\sprintf('%s.%s', PropertyPointTestClass::class, 'publicProperty')],
            [\sprintf('%s$%s', PropertyPointTestClass::class, 'publicProperty')],
            [\sprintf('%s.$', PropertyPointTestClass::class)],

            // Invalid reflection...
            [\sprintf('%s.$%s', 'InvalidClass', 'publicProperty')],
            [\sprintf('%s.$%s', PropertyPointTestClass::class, 'invalidProperty')],
        ];
    }

    private function buildPropertyPoint(string $className, string $propertyName): PropertyPoint
    {
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
