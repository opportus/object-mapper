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
 * The test object B.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class TestObjectB
{
    use TestObjectTrait;

    private $p;
    private $q;
    protected $r;
    public $s;
    public $t;

    public function getP(): int
    {
        return $this->p;
    }

    public function setP(int $p)
    {
        $this->p = $p;
    }

    public function getS(): int
    {
        return $this->s;
    }

    public function setS(int $s)
    {
        $this->s = $s;
    }
}
