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
use Opportus\ObjectMapper\Point\DynamicTargetPointInterface;
use Opportus\ObjectMapper\Point\MethodParameterDynamicTargetPoint;
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use Opportus\ObjectMapper\Tests\Test;
use Opportus\ObjectMapper\Tests\TestInvalidArgumentException;

/**
 * The method parameter dynamic target point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodParameterDynamicTargetPointTest extends Test
{
    private const FQN_REGEX_PATTERN = '/^~?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/';

    /**
     * @dataProvider provideMethodParameterDynamicTargetPointFqn
     */
    public function testConstruct(string $fqn): void
    {
        $point = $this->createMethodParameterDynamicTargetPoint($fqn);

        static::assertInstanceOf(MethodParameterDynamicTargetPoint::class, $point);
        static::assertInstanceOf(DynamicTargetPointInterface::class, $point);
        static::assertInstanceOf(TargetPointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider provideInvalidMethodParameterDynamicTargetPointFqn
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createMethodParameterDynamicTargetPoint($fqn);
    }

    /**
     * @dataProvider provideMethodParameterDynamicTargetPointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = $this->createMethodParameterDynamicTargetPoint($fqn);

        static::assertRegExp(self::FQN_REGEX_PATTERN, $point->getFqn());

        static::assertSame(
            \sprintf(
                '~%s::%s()::$%s',
                $this->getPointTargetFqn($fqn),
                $this->getPointMethodName($fqn),
                $this->getPointName($fqn)
            ),
            $point->getFqn()
        );
    }

    /**
     * @dataProvider provideMethodParameterDynamicTargetPointFqn
     */
    public function testGetTargetFqn(string $fqn): void
    {
        $point = $this->createMethodParameterDynamicTargetPoint($fqn);

        static::assertSame(
            $point->getTargetFqn(),
            $this->getPointTargetFqn($fqn)
        );
    }

    /**
     * @dataProvider provideMethodParameterDynamicTargetPointFqn
     */
    public function testGetName(string $fqn): void
    {
        $point = $this->createMethodParameterDynamicTargetPoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    /**
     * @dataProvider provideMethodParameterDynamicTargetPointFqn
     */
    public function testGetMethodName(string $fqn): void
    {
        $point = $this->createMethodParameterDynamicTargetPoint($fqn);

        static::assertSame(
            $point->getMethodName(),
            $this->getPointMethodName($fqn)
        );
    }

    private function getPointTargetFqn(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match method parameter dynamic target point FQN regex pattern %s, got %s.',
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
                'The argument must match method parameter dynamic target point FQN regex pattern %s, got %s.',
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
                'The argument must match method parameter dynamic target point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[3];
    }

    private function createMethodParameterDynamicTargetPoint(
        string $fqn
    ): MethodParameterDynamicTargetPoint {
        return new MethodParameterDynamicTargetPoint($fqn);
    }
}
