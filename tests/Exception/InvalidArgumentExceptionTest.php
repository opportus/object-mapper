<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Exception;

use Opportus\ObjectMapper\Exception\Exception;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * The invalid argument exception test.
 *
 * @package Opportus\ObjectMapper\Tests\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class InvalidArgumentExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $exception = new InvalidArgumentException(1);

        static::assertInstanceOf(Exception::class, $exception);
    }

    public function testGetArgument(): void
    {
        $exception = new InvalidArgumentException(1);

        static::assertSame(1, $exception->getArgument());
    }
}
