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
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\IterableRecursionCheckPoint;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Tests\ObjectA;
use Opportus\ObjectMapper\Tests\ObjectB;
use PHPUnit\Framework\TestCase;

/**
 * The iterable recursion check point test.
 *
 * @todo Test Control method
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class IterableRecursionCheckPointTest extends TestCase
{
    /**
     * @dataProvider provideConstructArguments
     */
    public function testConstruct(
        string $sourceFqn,
        string $targetFqn,
        SourcePointInterface $targetIterableSourcePoint
    ): void {
        $point = new IterableRecursionCheckPoint(
            $sourceFqn,
            $targetFqn,
            $targetIterableSourcePoint
        );

        static::assertInstanceOf(CheckPointInterface::class, $point);
    }

    /**
     * @dataProvider provideConstructInvalidArguments
     */
    public function testConstructException(
        string $sourceFqn,
        string $targetFqn,
        SourcePointInterface $targetSourcePoint
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new IterableRecursionCheckPoint(
            $sourceFqn,
            $targetFqn,
            $targetSourcePoint
        );
    }

    public function provideConstructArguments(): array
    {
        return [
            [
                ObjectA::class,
                ObjectB::class,
                $this->getMockBuilder(SourcePointInterface::class)->getMock()
            ],
        ];
    }

    public function provideConstructInvalidArguments(): array
    {
        return [
            [
                ObjectA::class,
                'NonObject',
                $this->getMockBuilder(SourcePointInterface::class)->getMock()
            ],
            [
                'NonObject',
                ObjectB::class,
                $this->getMockBuilder(SourcePointInterface::class)->getMock()
            ],
            [
                'NonObject',
                'NonObject',
                $this->getMockBuilder(SourcePointInterface::class)->getMock()
            ],
        ];
    }
}