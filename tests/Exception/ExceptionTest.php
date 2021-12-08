<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Exception;

use Exception as BaseException;
use Opportus\ObjectMapper\Exception\Exception;
use Opportus\ObjectMapper\Tests\Test;

/**
 * The exception test.
 *
 * @package Opportus\ObjectMapper\Tests\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ExceptionTest extends Test
{
    public function testConstruct(): void
    {
        $exception = $this->createException();

        static::assertInstanceOf(BaseException::class, $exception);
    }

    private function createException(): Exception
    {
        return new Exception();
    }
}
