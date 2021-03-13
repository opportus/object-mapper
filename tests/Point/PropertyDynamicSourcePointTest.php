<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Point\DynamicSourcePointInterface;
use Opportus\ObjectMapper\Point\PropertyDynamicSourcePoint;
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Tests\Test;
use Opportus\ObjectMapper\Tests\TestInvalidArgumentException;

/**
 * The property dynamic source point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyDynamicSourcePointTest extends Test
{
    private const FQN_REGEX_PATTERN = '/^~?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/';

    /**
     * @dataProvider providePropertyDynamicSourcePointFqn
     */
    public function testConstruct(string $fqn): void
    {
        $point = $this->createPropertyDynamicSourcePoint($fqn);

        static::assertInstanceOf(PropertyDynamicSourcePoint::class, $point);
        static::assertInstanceOf(DynamicSourcePointInterface::class, $point);
        static::assertInstanceOf(SourcePointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider provideInvalidPropertyDynamicSourcePointFqn
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createPropertyDynamicSourcePoint($fqn);
    }

    /**
     * @dataProvider providePropertyDynamicSourcePointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = $this->createPropertyDynamicSourcePoint($fqn);

        static::assertRegExp(self::FQN_REGEX_PATTERN, $point->getFqn());

        static::assertSame(
            \sprintf(
                '~%s::$%s',
                $this->getPointSourceFqn($fqn),
                $this->getPointName($fqn)
            ),
            $point->getFqn()
        );
    }

    /**
     * @dataProvider providePropertyDynamicSourcePointFqn
     */
    public function testGetTargetFqn(string $fqn): void
    {
        $point = $this->createPropertyDynamicSourcePoint($fqn);

        static::assertSame(
            $point->getSourceFqn(),
            $this->getPointSourceFqn($fqn)
        );
    }

    /**
     * @dataProvider providePropertyDynamicSourcePointFqn
     */
    public function testGetName(string $fqn): void
    {
        $point = $this->createPropertyDynamicSourcePoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    private function getPointSourceFqn(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match property dynamic source point FQN regex pattern %s, got %s.',
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
                'The argument must match property dynamic source point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[2];
    }

    private function createPropertyDynamicSourcePoint(
        string $fqn
    ): PropertyDynamicSourcePoint {
        return new PropertyDynamicSourcePoint($fqn);
    }
}
