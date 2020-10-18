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
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\StaticTargetPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use Opportus\ObjectMapper\Tests\InvalidArgumentException as TestInvalidArgumentException;
use Opportus\ObjectMapper\Tests\ObjectA;
use PHPUnit\Framework\TestCase;

/**
 * The method parameter static target point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodParameterStaticTargetPointTest extends TestCase
{
    private const FQN_REGEX_PATTERN = '/^#?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/';

    /**
     * @dataProvider provideMethodParameterStaticTargetPointFqn
     */
    public function testConstruct(string $fqn): void {
        $point = new MethodParameterStaticTargetPoint($fqn);

        static::assertInstanceOf(MethodParameterStaticTargetPoint::class, $point);
        static::assertInstanceOf(StaticTargetPointInterface::class, $point);
        static::assertInstanceOf(TargetPointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider provideMethodParameterStaticTargetPointFqnException
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MethodParameterStaticTargetPoint($fqn);
    }

    /**
     * @dataProvider provideMethodParameterStaticTargetPointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = new MethodParameterStaticTargetPoint($fqn);

        static::assertRegExp(self::FQN_REGEX_PATTERN, $point->getFqn());

        static::assertSame(
            \sprintf(
                '#%s::%s()::$%s',
                $this->getPointTargetFqn($fqn),
                $this->getPointMethodName($fqn),
                $this->getPointName($fqn)
            ),
            $point->getFqn()
        );
    }

    /**
     * @dataProvider provideMethodParameterStaticTargetPointFqn
     */
    public function testGetTargetFqn(string $fqn): void
    {
        $point = new MethodParameterStaticTargetPoint($fqn);

        static::assertSame(
            $point->getTargetFqn(),
            $this->getPointTargetFqn($fqn)
        );
    }

    /**
     * @dataProvider provideMethodParameterStaticTargetPointFqn
     */
    public function testGetName(string $fqn): void
    {
        $point = new MethodParameterStaticTargetPoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    /**
     * @dataProvider provideMethodParameterStaticTargetPointFqn
     */
    public function testGetMethodName(string $fqn): void
    {
        $point = new MethodParameterStaticTargetPoint($fqn);

        static::assertSame(
            $point->getMethodName(),
            $this->getPointMethodName($fqn)
        );
    }

    public function provideMethodParameterStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setF',
                    'f'
                ),
            ],
        ];
    }

    public function provideMethodParameterStaticTargetPointFqnException(): array
    {
        return [
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    'NonObject',
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'nonMethod',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'nonParameter'
                ),
            ],
        ];
    }

    private function getPointTargetFqn(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match method parameter static target point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[1];
    }

    private function getPointMethodName(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match method parameter static target point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[2];
    }

    private function getPointName(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match method parameter static target point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[3];
    }
}
