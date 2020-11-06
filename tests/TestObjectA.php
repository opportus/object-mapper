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
 * The test object A.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class TestObjectA
{
    use TestObjectTrait;

    private $i;
    private $j;
    public $k;
    public $l;
    
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

        $this->m = 0;
        $this->n = 0;
        $this->o = 0;
        $this->p = 0;
    }

    public function getI()
    {
        return $this->i;
    }

    public function setI($i)
    {
        $this->i = $i;
    }

    public function getK()
    {
        return $this->k;
    }

    public function setK($k)
    {
        $this->k = $k;
    }
}
