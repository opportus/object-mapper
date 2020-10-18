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
use Opportus\ObjectMapper\Point\DynamicSourcePointInterface;
use Opportus\ObjectMapper\Point\MethodDynamicSourcePoint;
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Tests\InvalidArgumentException as TestInvalidArgumentException;
use Opportus\ObjectMapper\Tests\ObjectA;
use PHPUnit\Framework\TestCase;

/**
 * The method dynamic source point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodDynamicSourcePointTest extends TestCase
{
    private const FQN_REGEX_PATTERN = '/^~?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/';

    /**
     * @dataProvider provideMethodDynamicSourcePointFqn
     */
    public function testConstruct(string $fqn): void
    {
        $point = new MethodDynamicSourcePoint($fqn);

        static::assertInstanceOf(MethodDynamicSourcePoint::class, $point);
        static::assertInstanceOf(DynamicSourcePointInterface::class, $point);
        static::assertInstanceOf(SourcePointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider provideMethodDynamicSourcePointFqnException
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MethodDynamicSourcePoint($fqn);
    }

    /**
     * @dataProvider provideMethodDynamicSourcePointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = new MethodDynamicSourcePoint($fqn);

        static::assertRegExp(self::FQN_REGEX_PATTERN, $point->getFqn());

        static::assertSame(
            \sprintf(
                '~%s::%s()',
                $this->getPointSourceFqn($fqn),
                $this->getPointName($fqn)
            ),
            $point->getFqn()
        );
    }

    /**
     * @dataProvider provideMethodDynamicSourcePointFqn
     */
    public function testGetTargetFqn(string $fqn): void
    {
        $point = new MethodDynamicSourcePoint($fqn);

        static::assertSame(
            $point->getSourceFqn(),
            $this->getPointSourceFqn($fqn)
        );
    }

    /**
     * @dataProvider provideMethodDynamicSourcePointFqn
     */
    public function testGetName(string $fqn): void
    {
        $point = new MethodDynamicSourcePoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    public function provideMethodDynamicSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
        ];
    }

    public function provideMethodDynamicSourcePointFqnException(): array
    {
        return [
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
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
                    '%s::%s()',
                    'NonObject',
                    'getZ'
                ),
            ],
        ];
    }

    private function getPointSourceFqn(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match method dynamic source point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[1];
    }

    private function getPointName(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match method dynamic source point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[2];
    }
}
