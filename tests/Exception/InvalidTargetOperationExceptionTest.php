<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Exception;

use Opportus\ObjectMapper\Exception\InvalidTargetOperationException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Tests\Test;

/**
 * The invalid operation exception test.
 *
 * @package Opportus\ObjectMapper\Tests\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class InvalidObjectOperationExceptionTest extends Test
{
    public function testConstruct(): void
    {
        $exception = $this->createInvalidObjectOperationException();

        static::assertInstanceOf(InvalidOperationException::class, $exception);
    }

    private function createInvalidObjectOperationException(): InvalidTargetOperationException
    {
        return new InvalidTargetOperationException();
    }
}
