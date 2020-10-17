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
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use PHPUnit\Framework\TestCase;

/**
 * The method parameter static source point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodParameterStaticTargetPointTest extends TestCase
{
    /**
     * @dataProvider provideInvalidParameterPointFqns
     * @param $invalidParameterPointFqn
     * @throws InvalidArgumentException
     */
    public function testConstructException($invalidParameterPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MethodParameterStaticTargetPoint($invalidParameterPointFqn);
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
            MethodParameterStaticTargetPoint::class,
            new MethodParameterStaticTargetPoint(
                \sprintf(
                    '%s::%s()::$%s',
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
            \sprintf('#%s::%s()::$%s', $className, $methodName, $parameterName),
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

        static::assertSame($className, $parameterPoint->getTargetFqn());
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
                MethodParameterStaticTargetPointTestClass::class,
                'privateMethod',
                'privateMethodParameter',
            ],
            [
                MethodParameterStaticTargetPointTestClass::class,
                'protectedMethod',
                'protectedMethodParameter',
            ],
            [
                MethodParameterStaticTargetPointTestClass::class,
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
                '%s::%s::$%s',
                MethodParameterStaticTargetPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s::%s()::%s',
                MethodParameterStaticTargetPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s::%s::%s',
                MethodParameterStaticTargetPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],

            // Invalid reflection...
            [\sprintf(
                '%s::%s()::$%s',
                'InvalidClass',
                'publicMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s::%s()::$%s',
                MethodParameterStaticTargetPointTestClass::class,
                'invalidMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s::%s()::$%s',
                MethodParameterStaticTargetPointTestClass::class,
                'publicMethod',
                'invalidParameter'
            )],
        ];
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @return MethodParameterStaticTargetPoint
     * @throws InvalidArgumentException
     */
    private function buildParameterPoint(
        string $className,
        string $methodName,
        string $parameterName
    ): MethodParameterStaticTargetPoint {
        $parameterPointFqn = \sprintf(
            '%s::%s()::$%s',
            $className,
            $methodName,
            $parameterName
        );

        return new MethodParameterStaticTargetPoint($parameterPointFqn);
    }
}

/**
 * The parameter object point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodParameterStaticTargetPointTestClass
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
