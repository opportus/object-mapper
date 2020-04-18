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
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The parameter point test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ParameterPointTest extends FinalBypassTestCase
{
    /**
     * @dataProvider provideInvalidParameterPointFqns
     * @param $invalidParameterPointFqn
     * @throws InvalidArgumentException
     */
    public function testConstructException($invalidParameterPointFqn): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ParameterPoint($invalidParameterPointFqn);
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
            ParameterPoint::class,
            new ParameterPoint(
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
                ParameterPointTestClass::class,
                'privateMethod',
                'privateMethodParameter',
            ],
            [
                ParameterPointTestClass::class,
                'protectedMethod',
                'protectedMethodParameter',
            ],
            [
                ParameterPointTestClass::class,
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
            [\sprintf(
                '%s.%s.$%s',
                ParameterPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s.%s().%s',
                ParameterPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],
            [\sprintf(
                '%s.%s.%s',
                ParameterPointTestClass::class,
                'publicMethod',
                'publicMethodParameter'
            )],
        ];
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @return ParameterPoint
     * @throws InvalidArgumentException
     */
    private function buildParameterPoint(
        string $className,
        string $methodName,
        string $parameterName
    ): ParameterPoint {
        $parameterPointFqn = \sprintf(
            '%s.%s().$%s',
            $className,
            $methodName,
            $parameterName
        );

        return new ParameterPoint($parameterPointFqn);
    }
}

/**
 * The parameter point test class.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ParameterPointTestClass
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
