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
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Tests\Test;

/**
 * The invalid operation exception test.
 *
 * @package Opportus\ObjectMapper\Tests\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class InvalidOperationExceptionTest extends Test
{
    public function testConstruct(): void
    {
        $exception = $this->createInvalidOperationException();

        static::assertInstanceOf(Exception::class, $exception);
    }

    private function createInvalidOperationException(): InvalidOperationException
    {
        return new InvalidOperationException();
    }
}
