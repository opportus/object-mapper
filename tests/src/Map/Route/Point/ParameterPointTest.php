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
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The parameter point test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ParameterPointTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidParameterPointFqns
     */
    public function testConstructException($invalidParameterPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ParameterPoint($invalidParameterPointFqn);
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     */
    public function testConstruct(string $className, string $methodName, string $parameterName): void
    {
        $this->assertInstanceOf(ParameterPoint::class, new ParameterPoint(\sprintf('%s.%s().$%s', $className, $methodName, $parameterName)));
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     */
    public function testGetFqn(string $className, string $methodName, string $parameterName): void
    {
        $parameterPoint = $this->buildParameterPoint($className, $methodName, $parameterName);

        $this->assertSame(\sprintf('%s.%s().$%s', $className, $methodName, $parameterName), $parameterPoint->getFqn());
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     */
    public function testGetClassFqn(string $className, string $methodName, string $parameterName): void
    {
        $parameterPoint = $this->buildParameterPoint($className, $methodName, $parameterName);

        $this->assertSame($className, $parameterPoint->getClassFqn());
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     */
    public function testGetName(string $className, string $methodName, string $parameterName): void
    {
        $parameterPoint = $this->buildParameterPoint($className, $methodName, $parameterName);

        $this->assertSame($parameterName, $parameterPoint->getName());
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     */
    public function testGetMethodName(string $className, string $methodName, string $parameterName): void
    {
        $parameterPoint = $this->buildParameterPoint($className, $methodName, $parameterName);

        $this->assertSame($methodName, $parameterPoint->getMethodName());
    }
    /**
     * @dataProvider provideParameterPointFqnTokens
     */
    public function testGetPosition(string $className, string $methodName, string $parameterName): void
    {
        $parameterPoint = $this->buildParameterPoint($className, $methodName, $parameterName);

        $this->assertSame(0, $parameterPoint->getPosition());
    }

    public function provideParameterPointFqnTokens(): array
    {
        return [
            [ParameterPointTestClass::class, 'privateMethod',   'privateMethodParameter'],
            [ParameterPointTestClass::class, 'protectedMethod', 'protectedMethodParameter'],
            [ParameterPointTestClass::class, 'publicMethod',    'publicMethodParameter'],
        ];
    }

    public function provideInvalidParameterPointFqns(): array
    {
        return [
            // Invalid syntax...
            [\sprintf('%s.%s.$%s', ParameterPointTestClass::class, 'publicMethod', 'publicMethodParameter')],
            [\sprintf('%s.%s().%s', ParameterPointTestClass::class, 'publicMethod', 'publicMethodParameter')],
            [\sprintf('%s.%s.%s', ParameterPointTestClass::class, 'publicMethod', 'publicMethodParameter')],

            // Invalid reflection...
            [\sprintf('%s.%s().$%s', 'InvalidClass', 'publicMethod', 'publicMethodParameter')],
            [\sprintf('%s.%s().$%s', ParameterPointTestClass::class, 'invalidMethod', 'publicMethodParameter')],
            [\sprintf('%s.%s().$%s', ParameterPointTestClass::class, 'publicMethod', 'invalidParameter')],
        ];
    }

    private function buildParameterPoint(string $className, string $methodName, string $parameterName): ParameterPoint
    {
        $parameterPointFqn = \sprintf('%s.%s().$%s', $className, $methodName, $parameterName);

        return new ParameterPoint($parameterPointFqn);
    }
}

/**
 * The parameter point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ParameterPointTestClass
{
    private function privateMethod($privateMethodParameter)
    {
    }
    protected function protectedMethod($protectedMethodParameter)
    {
    }
    public function publicMethod($publicMethodParameter)
    {
    }
}
