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
     */
    public function testConstructException($invalidMethodPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MethodPoint($invalidMethodPointFqn);
    }

    /**
     * @dataProvider provideMethodPointFqnTokens
     */
    public function testConstruct(string $className, string $methodName): void
    {
        $this->assertInstanceOf(MethodPoint::class, new MethodPoint(\sprintf('%s.%s()', $className, $methodName)));
    }

    /**
     * @dataProvider provideMethodPointFqnTokens
     */
    public function testGetFqn(string $className, string $methodName): void
    {
        $methodPoint = $this->buildMethodPoint($className, $methodName);

        $this->assertSame(\sprintf('%s.%s()', $className, $methodName), $methodPoint->getFqn());
    }

    /**
     * @dataProvider provideMethodPointFqnTokens
     */
    public function testGetClassFqn(string $className, string $methodName): void
    {
        $methodPoint = $this->buildMethodPoint($className, $methodName);

        $this->assertSame($className, $methodPoint->getClassFqn());
    }

    /**
     * @dataProvider provideMethodPointFqnTokens
     */
    public function testGetName(string $className, string $methodName): void
    {
        $methodPoint = $this->buildMethodPoint($className, $methodName);

        $this->assertSame($methodName, $methodPoint->getName());
    }

    public function provideMethodPointFqnTokens(): array
    {
        return [
            [MethodPointTestClass::class, 'privateMethod'],
            [MethodPointTestClass::class, 'protectedMethod'],
            [MethodPointTestClass::class, 'publicMethod'],
        ];
    }

    public function provideInvalidMethodPointFqns(): array
    {
        return [
            // Invalid syntax...
            [\sprintf('%s.%s', MethodPointTestClass::class, 'publicMethod')],
            [\sprintf('%s%s()', MethodPointTestClass::class, 'publicMethod')],
            [\sprintf('%s.', MethodPointTestClass::class)],

            // Invalid reflection...
            [\sprintf('%s.%s()', 'InvalidClass', 'publicMethod')],
            [\sprintf('%s.%s()', MethodPointTestClass::class, 'invalidMethod')],

            // Invalid method...
            [\sprintf('%s.%s()', MethodPointTestClass::class, 'parameterableMethod')],
        ];
    }

    private function buildMethodPoint(string $className, string $methodName): MethodPoint
    {
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
    private function privateMethod(): int
    {
        return 1;
    }

    protected function protectedMethod(): int
    {
        return 1;
    }

    public function publicMethod(): int
    {
        return 1;
    }

    public function parameterableMethod($parameterableMethodParameter): int
    {
        return 1;
    }
}
