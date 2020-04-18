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
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The method point test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodPointTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidMethodPointFqns
     * @param $invalidMethodPointFqn
     * @throws InvalidArgumentException
     */
    public function testConstructException($invalidMethodPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MethodPoint($invalidMethodPointFqn);
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
            MethodPoint::class,
            new MethodPoint(\sprintf('%s.%s()', $className, $methodName))
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
            [MethodPointTestClass::class, 'privateMethod'],
            [MethodPointTestClass::class, 'protectedMethod'],
            [MethodPointTestClass::class, 'publicMethod'],
        ];
    }

    /**
     * @return array|array[]
     */
    public function provideInvalidMethodPointFqns(): array
    {
        return [
            [\sprintf(
                '%s.%s',
                MethodPointTestClass::class,
                'publicMethod'
            )],
            [\sprintf(
                '%s%s()',
                MethodPointTestClass::class,
                'publicMethod'
            )],
            [\sprintf(
                '%s.',
                MethodPointTestClass::class
            )],
        ];
    }

    /**
     * @param string $className
     * @param string $methodName
     * @return MethodPoint
     * @throws InvalidArgumentException
     */
    private function buildMethodPoint(
        string $className,
        string $methodName
    ): MethodPoint {
        $methodPointFqn = \sprintf('%s.%s()', $className, $methodName);

        return new MethodPoint($methodPointFqn);
    }
}

/**
 * The method point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodPointTestClass
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

    /**
     * @param $parameterableMethodParameter
     * @return int
     */
    public function parameterableMethod($parameterableMethodParameter): int
    {
        return 1;
    }
}
