<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

/**
 * The static point interface.
 *
 * @package Opportus\ObjectMapper\Point
 * @author Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface StaticPointInterface extends ObjectPointInterface
{
    /**
     * Gets the PHP types of the point's value.
     *
     * @return string[] An array containing string elements representing the PHP types of the point's value.
     * @see https://www.php.net/manual/en/language.types.php
     */
    public function getValuePhpTypes(): array;

    /**
     * Gets the PHPDoc types (non FQCN resolved) of the point's value.
     *
     * @return string[] An array containing string elements representing the PHPDoc types of the point's value.
     *                  Each element of the array represents
     * @see https://docs.phpdoc.org/guide/guides/types.html
     */
    public function getValuePhpDocTypes(): array;
}
