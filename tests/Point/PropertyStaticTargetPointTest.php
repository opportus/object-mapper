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
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\StaticTargetPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use Opportus\ObjectMapper\Tests\Test;
use Opportus\ObjectMapper\Tests\TestInvalidArgumentException;

/**
 * The property static target point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyStaticTargetPointTest extends Test
{
    private const FQN_REGEX_PATTERN = '/^#?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/';

    /**
     * @dataProvider providePropertyStaticTargetPointFqn
     */
    public function testConstruct(string $fqn): void
    {
        $point = $this->createPropertyStaticTargetPoint($fqn);

        static::assertInstanceOf(PropertyStaticTargetPoint::class, $point);
        static::assertInstanceOf(StaticTargetPointInterface::class, $point);
        static::assertInstanceOf(TargetPointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider provideInvalidPropertyStaticTargetPointFqn
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createPropertyStaticTargetPoint($fqn);
    }

    /**
     * @dataProvider providePropertyStaticTargetPointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = $this->createPropertyStaticTargetPoint($fqn);

        static::assertMatchesRegularExpression(self::FQN_REGEX_PATTERN, $point->getFqn());

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
        $point = $this->createPropertyStaticTargetPoint($fqn);

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
        $point = $this->createPropertyStaticTargetPoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    private function getPointTargetFqn(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match property static target point FQN regex pattern %s, got %s.',
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
                'The argument must match property static target point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[2];
    }

    private function createPropertyStaticTargetPoint(
        string $fqn
    ): PropertyStaticTargetPoint {
        return new PropertyStaticTargetPoint($fqn);
    }
}
