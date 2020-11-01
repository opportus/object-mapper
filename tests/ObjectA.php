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

/**
 * The object A.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectA
{
    use ObjectTrait;

    private $j;
    private $k;
    protected $l;
    public $m;
    public $n;

    public function getJ(): int
    {
        return $this->j;
    }

    public function setJ(int $j)
    {
        $this->j = $j;
    }

    public function getM(): int
    {
        return $this->m;
    }

    public function setM(int $m)
    {
        $this->m = $m;
    }

    public function __call($name, $value)
    {
    }
}
