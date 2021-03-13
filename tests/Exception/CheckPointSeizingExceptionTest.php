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

use Opportus\ObjectMapper\Exception\CheckPointSeizingException;
use Opportus\ObjectMapper\Exception\Exception;
use Opportus\ObjectMapper\Tests\Test;

/**
 * The check point seizing exception test.
 *
 * @package Opportus\ObjectMapper\Tests\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class CheckPointSeizingExceptionTest extends Test
{
    public function testConstruct(): void
    {
        $exception = $this->createCheckPointSeizingException();

        static::assertInstanceOf(Exception::class, $exception);
    }

    private function createCheckPointSeizingException(): CheckPointSeizingException
    {
        return new CheckPointSeizingException();
    }
}
