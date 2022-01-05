<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Point\StaticSourcePointInterface;
use Opportus\ObjectMapper\Tests\Test;
use Opportus\ObjectMapper\Tests\TestInvalidArgumentException;

/**
 * The property static source point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyStaticSourcePointTest extends Test
{
    private const FQN_REGEX_PATTERN = '/^#?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/';

    /**
     * @dataProvider providePropertyStaticSourcePointFqn
     */
    public function testConstruct(string $fqn): void
    {
        $point = $this->createPropertyStaticSourcePoint($fqn);

        static::assertInstanceOf(PropertyStaticSourcePoint::class, $point);
        static::assertInstanceOf(StaticSourcePointInterface::class, $point);
        static::assertInstanceOf(SourcePointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider provideInvalidPropertyStaticSourcePointFqn
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createPropertyStaticSourcePoint($fqn);
    }

    /**
     * @dataProvider providePropertyStaticSourcePointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = $this->createPropertyStaticSourcePoint($fqn);

        static::assertMatchesRegularExpression(self::FQN_REGEX_PATTERN, $point->getFqn());

        static::assertSame(
            \sprintf(
                '#%s::$%s',
                $this->getPointSourceFqn($fqn),
                $this->getPointName($fqn)
            ),
            $point->getFqn()
        );
    }

    /**
     * @dataProvider providePropertyStaticSourcePointFqn
     */
    public function testGetTargetFqn(string $fqn): void
    {
        $point = $this->createPropertyStaticSourcePoint($fqn);

        static::assertSame(
            $point->getSourceFqn(),
            $this->getPointSourceFqn($fqn)
        );
    }

    /**
     * @dataProvider providePropertyStaticSourcePointFqn
     */
    public function testGetName(string $fqn): void
    {
        $point = $this->createPropertyStaticSourcePoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    private function getPointSourceFqn(string $fqn): string
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

    private function createPropertyStaticSourcePoint(
        string $fqn
    ): PropertyStaticSourcePoint {
        return new PropertyStaticSourcePoint($fqn);
    }
}
