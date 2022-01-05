<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Exception;

use Throwable;

/**
 * The invalid object operation exception.
 *
 * @package Opportus\ObjectMapper\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class InvalidTargetOperationException extends InvalidOperationException
{
    /**
     * Constructs the invalid object operation exception.
     *
     * @param string $message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $message = \sprintf(
            'Object operation is invalid. %s',
            $message
        );

        parent::__construct($message, $code, $previous);
    }
}
