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
use Opportus\ObjectMapper\Point\ParameterObjectPoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The parameter object point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ParameterObjectPointTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidParameterPointFqns
     * @param $invalidParameterPointFqn
     * @throws InvalidArgumentException
     */
    public function testConstructException($invalidParameterPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ParameterObjectPoint($invalidParameterPointFqn);
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @throws InvalidArgumentException
     */
    public function testConstruct(
        string $className,
        string $methodName,
        string $parameterName
    ): void {
        static::assertInstanceOf(
            ParameterObjectPoint::class,
            new ParameterObjectPoint(
                \sprintf(
                    '%s.%s().$%s',
                    $className,
                    $methodName,
                    $parameterName
                )
            )
        );
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @throws InvalidArgumentException
     */
    public function testGetFqn(
        string $className,
        string $methodName,
        string $parameterName
    ): void {
        $parameterPoint = $this->buildParameterPoint(
            $className,
            $methodName,
            $parameterName
        );

        static::assertSame(
            \sprintf('%s.%s().$%s', $className, $methodName, $parameterName),
            $parameterPoint->getFqn()
        );
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @throws InvalidArgumentException
     */
    public function testGetClassFqn(
        string $className,
        string $methodName,
        string $parameterName
    ): void {
        $parameterPoint = $this->buildParameterPoint(
            $className,
            $methodName,
            $parameterName
        );

        static::assertSame($className, $parameterPoint->getClassFqn());
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @throws InvalidArgumentException
     */
    public function testGetName(
        string $className,
        string $methodName,
        string $parameterName
    ): void {
        $parameterPoint = $this->buildParameterPoint(
            $className,
            $methodName,
            $parameterName
        );

        static::assertSame($parameterName, $parameterPoint->getName());
    }

    /**
     * @dataProvider provideParameterPointFqnTokens
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @throws InvalidArgumentException
     */
    public function testGetMethodName(
        string $className,
        string $methodName,
        string $parameterName
    ): void {
        $parameterPoint = $this->buildParameterPoint(
            $className,
            $methodName,
            $parameterName
        );

        static::assertSame($methodName, $parameterPoint->getMethodName());
    }

    /**
     * @return array|string[][]
     */
    public function provideParameterPointFqnTokens(): array
    {
        return [
            [
                ParameterObjectPointTestClass::class,
                'privateMethod',
                'privateMethodParameter',
            ],
            [
                ParameterObjectPointTestClass::class,
                'protectedMethod',
                'protectedMethodParameter',
            ],
            [
                ParameterObjectPointTestClass::class,
                'publicMethod',
                'publicMethodParameter',
            ],
        ];
    }

    /**
     * @return array|array[]
     */
    public function provideInvalidParameterPointFqns(): array
    {
        return [
            // Invalid syntax...
            [\sprintf(
                '%s.%s.$%s',
                ParameterObjectPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s.%s().%s',
                ParameterObjectPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s.%s.%s',
                ParameterObjectPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],

            // Invalid reflection...
            [\sprintf(
                '%s.%s().$%s',
                'InvalidClass',
                'publicMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s.%s().$%s',
                ParameterObjectPointTestClass::class,
                'invalidMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s.%s().$%s',
                ParameterObjectPointTestClass::class,
                'publicMethod',
                'invalidParameter'
            )],
        ];
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @return ParameterObjectPoint
     * @throws InvalidArgumentException
     */
    private function buildParameterPoint(
        string $className,
        string $methodName,
        string $parameterName
    ): ParameterObjectPoint {
        $parameterPointFqn = \sprintf(
            '%s.%s().$%s',
            $className,
            $methodName,
            $parameterName
        );

        return new ParameterObjectPoint($parameterPointFqn);
    }
}

/**
 * The parameter object point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ParameterObjectPointTestClass
{
    /**
     * @param $privateMethodParameter
     */
    private function privateMethod($privateMethodParameter)
    {
    }

    /**
     * @param $protectedMethodParameter
     */
    protected function protectedMethod($protectedMethodParameter)
    {
    }

    /**
     * @param $publicMethodParameter
     */
    public function publicMethod($publicMethodParameter)
    {
    }
}
