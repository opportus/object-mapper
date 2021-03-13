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
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\RecursionCheckPoint;
use Opportus\ObjectMapper\Point\SourcePointInterface;
use Opportus\ObjectMapper\Tests\Test;
use Opportus\ObjectMapper\Tests\TestObjectA;
use Opportus\ObjectMapper\Tests\TestObjectB;

/**
 * The recursion check point test.
 *
 * @todo Test Control method
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RecursionCheckPointTest extends Test
{
    /**
     * @dataProvider provideConstructArguments
     */
    public function testConstruct(
        string $sourceFqn,
        string $targetFqn,
        SourcePointInterface $targetSourcePoint
    ): void {
        $point = $this->createRecursionCheckPoint(
            $sourceFqn,
            $targetFqn,
            $targetSourcePoint
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

        $this->createRecursionCheckPoint(
            $sourceFqn,
            $targetFqn,
            $targetSourcePoint
        );
    }

    public function provideConstructArguments(): array
    {
        return [
            [
                TestObjectA::class,
                TestObjectB::class,
                $this->getMockBuilder(SourcePointInterface::class)->getMock(),
            ],
        ];
    }

    public function provideConstructInvalidArguments(): array
    {
        return [
            [
                TestObjectA::class,
                'NonObject',
                $this->getMockBuilder(SourcePointInterface::class)->getMock(),
            ],
            [
                'NonObject',
                TestObjectB::class,
                $this->getMockBuilder(SourcePointInterface::class)->getMock(),
            ],
            [
                'NonObject',
                'NonObject',
                $this->getMockBuilder(SourcePointInterface::class)->getMock(),
            ],
        ];
    }

    private function createRecursionCheckPoint(
        string $sourceFqn,
        string $targetFqn,
        SourcePointInterface $targetSourcePoint
    ): RecursionCheckPoint {
        return new RecursionCheckPoint(
            $sourceFqn,
            $targetFqn,
            $targetSourcePoint
        );
    }
}
