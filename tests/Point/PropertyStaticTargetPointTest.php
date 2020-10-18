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
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\StaticTargetPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use Opportus\ObjectMapper\Tests\InvalidArgumentException as TestInvalidArgumentException;
use Opportus\ObjectMapper\Tests\ObjectA;
use PHPUnit\Framework\TestCase;

/**
 * The property static target point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyStaticTargetPointTest extends TestCase
{
    private const FQN_REGEX_PATTERN = '/^#?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/';

    /**
     * @dataProvider providePropertyStaticTargetPointFqn
     */
    public function testConstruct(string $fqn): void
    {
        $point = new PropertyStaticTargetPoint($fqn);

        static::assertInstanceOf(PropertyStaticTargetPoint::class, $point);
        static::assertInstanceOf(StaticTargetPointInterface::class, $point);
        static::assertInstanceOf(TargetPointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider providePropertyStaticTargetPointFqnException
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PropertyStaticTargetPoint($fqn);
    }

    /**
     * @dataProvider providePropertyStaticTargetPointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = new PropertyStaticTargetPoint($fqn);

        static::assertRegExp(self::FQN_REGEX_PATTERN, $point->getFqn());

        static::assertSame(
            \sprintf(
                '#%s::$%s',
                $this->getPointTargetFqn($fqn),
                $this->getPointName($fqn)
            ),
            $point->getFqn()
        );
    }

    /**
     * @dataProvider providePropertyStaticTargetPointFqn
     */
    public function testGetTargetFqn(string $fqn): void
    {
        $point = new PropertyStaticTargetPoint($fqn);

        static::assertSame(
            $point->getTargetFqn(),
            $this->getPointTargetFqn($fqn)
        );
    }

    /**
     * @dataProvider providePropertyStaticTargetPointFqn
     */
    public function testGetName(string $fqn): void
    {
        $point = new PropertyStaticTargetPoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    public function providePropertyStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'e'
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
                    '#%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
        ];
    }

    public function providePropertyStaticTargetPointFqnException(): array
    {
        return [
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
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
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    'NonObject',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'nonProperty'
                ),
            ],
        ];
    }

    private function getPointTargetFqn(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match property static source point FQN regex pattern %s, got %s.',
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
                'The argument must match property static source point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[2];
    }
}
