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
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The method static source point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodStaticSourcePointTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidMethodPointFqns
     * @param $invalidMethodPointFqn
     * @throws InvalidArgumentException
     */
    public function testConstructException($invalidMethodPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MethodStaticSourcePoint($invalidMethodPointFqn);
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
            MethodStaticSourcePoint::class,
            new MethodStaticSourcePoint(\sprintf('%s.%s()', $className, $methodName))
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

        static::assertSame($className, $methodPoint->getSourceFqn());
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
            [MethodStaticSourcePointTestClass::class, 'privateMethod'],
            [MethodStaticSourcePointTestClass::class, 'protectedMethod'],
            [MethodStaticSourcePointTestClass::class, 'publicMethod'],
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
                MethodStaticSourcePointTestClass::class,
                'publicMethod'
            )],
            [\sprintf(
                '%s%s()',
                MethodStaticSourcePointTestClass::class,
                'publicMethod'
            )],
            [\sprintf(
                '%s.',
                MethodStaticSourcePointTestClass::class
            )],

            // Invalid reflection...
            [\sprintf(
                '%s.%s()',
                'InvalidClass',
                'publicMethod'
            )],
            [\sprintf(
                '%s.%s()',
                MethodStaticSourcePointTestClass::class,
                'invalidMethod'
            )],
        ];
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return MethodStaticSourcePoint
     * @throws InvalidArgumentException
     */
    private function buildMethodPoint(
        string $className,
        string $methodName
    ): MethodStaticSourcePoint {
        $methodPointFqn = \sprintf('%s.%s()', $className, $methodName);

        return new MethodStaticSourcePoint($methodPointFqn);
    }
}

/**
 * The method object point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodStaticSourcePointTestClass
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
