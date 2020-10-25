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
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\StaticSourcePointInterface;
use Opportus\ObjectMapper\Tests\InvalidArgumentException as TestInvalidArgumentException;
use Opportus\ObjectMapper\Tests\ProviderTrait;
use PHPUnit\Framework\TestCase;

/**
 * The method static source point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodStaticSourcePointTest extends TestCase
{
    use ProviderTrait;

    private const FQN_REGEX_PATTERN = '/^#?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/';

    /**
     * @dataProvider provideMethodStaticSourcePointFqn
     */
    public function testConstruct(string $fqn): void
    {
        $point = new MethodStaticSourcePoint($fqn);

        static::assertInstanceOf(MethodStaticSourcePoint::class, $point);
        static::assertInstanceOf(StaticSourcePointInterface::class, $point);
        static::assertInstanceOf(SourcePointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider provideInvalidMethodStaticSourcePointFqn
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        new MethodStaticSourcePoint($fqn);
    }

    /**
     * @dataProvider provideMethodStaticSourcePointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = new MethodStaticSourcePoint($fqn);

        static::assertRegExp(self::FQN_REGEX_PATTERN, $point->getFqn());

        static::assertSame(
            \sprintf(
                '#%s::%s()',
                $this->getPointSourceFqn($fqn),
                $this->getPointName($fqn)
            ),
            $point->getFqn()
        );
    }

    /**
     * @dataProvider provideMethodStaticSourcePointFqn
     */
    public function testGetTargetFqn(string $fqn): void
    {
        $point = new MethodStaticSourcePoint($fqn);

        static::assertSame(
            $point->getSourceFqn(),
            $this->getPointSourceFqn($fqn)
        );
    }

    /**
     * @dataProvider provideMethodStaticSourcePointFqn
     */
    public function testGetName(string $fqn): void
    {
        $point = new MethodStaticSourcePoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    private function getPointSourceFqn(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match method static source point FQN regex pattern %s, got %s.',
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
                'The argument must match method static source point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[2];
    }
}
