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
use Opportus\ObjectMapper\Point\DynamicTargetPointInterface;
use Opportus\ObjectMapper\Point\PropertyDynamicTargetPoint;
use Opportus\ObjectMapper\Point\ObjectPointInterface;
use Opportus\ObjectMapper\Point\TargetPointInterface;
use Opportus\ObjectMapper\Tests\Test;
use Opportus\ObjectMapper\Tests\TestInvalidArgumentException;

/**
 * The property dynamic target point test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyDynamicTargetPointTest extends Test
{
    private const FQN_REGEX_PATTERN = '/^~?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/';

    /**
     * @dataProvider providePropertyDynamicTargetPointFqn
     */
    public function testConstruct(string $fqn): void
    {
        $point = $this->createPropertyDynamicTargetPoint($fqn);

        static::assertInstanceOf(PropertyDynamicTargetPoint::class, $point);
        static::assertInstanceOf(DynamicTargetPointInterface::class, $point);
        static::assertInstanceOf(TargetPointInterface::class, $point);
        static::assertInstanceOf(ObjectPointInterface::class, $point);
    }

    /**
     * @dataProvider provideInvalidPropertyDynamicTargetPointFqn
     */
    public function testConstructException(string $fqn): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createPropertyDynamicTargetPoint($fqn);
    }

    /**
     * @dataProvider providePropertyDynamicTargetPointFqn
     */
    public function testGetFqn(string $fqn): void
    {
        $point = $this->createPropertyDynamicTargetPoint($fqn);

        static::assertRegExp(self::FQN_REGEX_PATTERN, $point->getFqn());

        static::assertSame(
            \sprintf(
                '~%s::$%s',
                $this->getPointTargetFqn($fqn),
                $this->getPointName($fqn)
            ),
            $point->getFqn()
        );
    }

    /**
     * @dataProvider providePropertyDynamicTargetPointFqn
     */
    public function testGetTargetFqn(string $fqn): void
    {
        $point = $this->createPropertyDynamicTargetPoint($fqn);

        static::assertSame(
            $point->getTargetFqn(),
            $this->getPointTargetFqn($fqn)
        );
    }

    /**
     * @dataProvider providePropertyDynamicTargetPointFqn
     */
    public function testGetName(string $fqn): void
    {
        $point = $this->createPropertyDynamicTargetPoint($fqn);

        static::assertSame($point->getName(), $this->getPointName($fqn));
    }

    private function getPointTargetFqn(string $fqn): string
    {
        if (false === \preg_match(self::FQN_REGEX_PATTERN, $fqn, $matches)) {
            $message = \sprintf(
                'The argument must match property dynamic target point FQN regex pattern %s, got %s.',
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
                'The argument must match property dynamic target point FQN regex pattern %s, got %s.',
                self::FQN_REGEX_PATTERN,
                $fqn
            );

            throw new TestInvalidArgumentException(1, __METHOD__, $message);
        }

        return $matches[2];
    }

    private function createPropertyDynamicTargetPoint(
        string $fqn
    ): PropertyDynamicTargetPoint {
        return new PropertyDynamicTargetPoint($fqn);
    }
}
