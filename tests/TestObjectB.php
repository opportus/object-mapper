<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
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

    private $m;
    private $n;
    public $o;
    public $p;

    public function __construct(
        $a = null,
        $b = null,
        $c = null,
        $d = null,
        $e = null,
        $f = null,
        $g = null,
        $h = null
    ) {
        $this->initialize($a, $b, $c, $d, $e, $f, $g, $h);

        $this->i = 0;
        $this->j = 0;
        $this->k = 0;
        $this->l = 0;
    }

    public function getM()
    {
        return $this->m;
    }

    public function setM($m)
    {
        $this->m = $m;
    }

    public function getO()
    {
        return $this->o;
    }

    public function setO($o)
    {
        $this->o = $o;
    }
}
