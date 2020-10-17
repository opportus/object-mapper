<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests;

use Exception;
use Throwable;

/**
 * The invalid argument exception.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class InvalidArgumentException extends Exception
{
    /**
     * Constructs the invalid argument exception.
     *
     * @param int $argument
     * @param string $function
     * @param string $message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        int $argument,
        string $function,
        string $message,
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = \sprintf(
            'Argument %d passed to %s is invalid. %s',
            $argument,
            $function,
            $message
        );

        parent::__construct($message, $code, $previous);
    }
}
