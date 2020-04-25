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
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The method object point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodObjectPointTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidMethodPointFqns
     * @param $invalidMethodPointFqn
     * @throws InvalidArgumentException
     */
    public function testConstructException($invalidMethodPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MethodObjectPoint($invalidMethodPointFqn);
    }

    /**
     * @dataProvider provideMethodPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @throws InvalidArgumentException
     */
    public function testConstruct(string $className, string $methodName): void
    {
        static::assertInstanceOf(
            MethodObjectPoint::class,
            new MethodObjectPoint(\sprintf('%s.%s()', $className, $methodName))
        );
    }

    /**
     * @dataProvider provideMethodPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @throws InvalidArgumentException
     */
    public function testGetFqn(string $className, string $methodName): void
    {
        $methodPoint = $this->buildMethodPoint($className, $methodName);

        static::assertSame(
            \sprintf('%s.%s()', $className, $methodName),
            $methodPoint->getFqn()
        );
    }

    /**
     * @dataProvider provideMethodPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @throws InvalidArgumentException
     */
    public function testGetClassFqn(string $className, string $methodName): void
    {
        $methodPoint = $this->buildMethodPoint($className, $methodName);

        static::assertSame($className, $methodPoint->getClassFqn());
    }

    /**
     * @dataProvider provideMethodPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @throws InvalidArgumentException
     */
    public function testGetName(string $className, string $methodName): void
    {
        $methodPoint = $this->buildMethodPoint($className, $methodName);

        static::assertSame($methodName, $methodPoint->getName());
    }

    /**
     * @return array|string[][]
     */
    public function provideMethodPointFqnTokens(): array
    {
        return [
            [MethodObjectPointTestClass::class, 'privateMethod'],
            [MethodObjectPointTestClass::class, 'protectedMethod'],
            [MethodObjectPointTestClass::class, 'publicMethod'],
        ];
    }

    /**
     * @return array|array[]
     */
    public function provideInvalidMethodPointFqns(): array
    {
        return [
            // Invalid syntax...
            [\sprintf(
                '%s.%s',
                MethodObjectPointTestClass::class,
                'publicMethod'
            )],
            [\sprintf(
                '%s%s()',
                MethodObjectPointTestClass::class,
                'publicMethod'
            )],
            [\sprintf(
                '%s.',
                MethodObjectPointTestClass::class
            )],

            // Invalid reflection...
            [\sprintf(
                '%s.%s()',
                'InvalidClass',
                'publicMethod'
            )],
            [\sprintf(
                '%s.%s()',
                MethodObjectPointTestClass::class,
                'invalidMethod'
            )],
        ];
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return MethodObjectPoint
     * @throws InvalidArgumentException
     */
    private function buildMethodPoint(
        string $className,
        string $methodName
    ): MethodObjectPoint {
        $methodPointFqn = \sprintf('%s.%s()', $className, $methodName);

        return new MethodObjectPoint($methodPointFqn);
    }
}

/**
 * The method object point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodObjectPointTestClass
{
    /**
     * @return int
     */
    private function privateMethod(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    protected function protectedMethod(): int
    {
        return 1;
    }

    /**
     * @return int
     */
    public function publicMethod(): int
    {
        return 1;
    }
}
