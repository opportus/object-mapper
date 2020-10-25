<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Exception;

use Throwable;

/**
 * The invalid argument exception.
 *
 * @package Opportus\ObjectMapper\Exception
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class InvalidArgumentException extends Exception
{
    /**
     * @var int $argument
     */
    private $argument;

    /**
     * @var string $function
     */
    private $function;

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
        $this->argument = $argument;
        $this->function = $function;

        $message = \sprintf(
            'Argument %d passed to %s is invalid. %s',
            $this->argument,
            $this->function,
            $message
        );

        parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the argument.
     *
     * @return int
     */
    public function getArgument(): int
    {
        return $this->argument;
    }

    /**
     * Gets the function.
     *
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }
}
