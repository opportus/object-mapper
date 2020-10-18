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
 * The object trait.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
trait ObjectTrait
{
    private $a;
    private $b;
    private $c;
    private $d;
    protected $e;
    public $f;

    public function __construct(int $a = 22)
    {
        $this->a = $a;
    }

    public function getA(): int
    {
        return $this->a;
    }

    public function setA(int $a)
    {
        $this->a = $a;
    }

    public function getB(): int
    {
        return $this->b;
    }

    public function setB(int $b)
    {
        $this->b = $b;
    }

    public function getC()
    {
        return $this->c;
    }

    public function setC(object $c)
    {
        return $this->c = $c;
    }

    public function getD(): array
    {
        return $this->d;
    }

    public function setD(array $d)
    {
        return $this->d = $d;
    }

    protected function getE(): int
    {
        return $this->e;
    }

    protected function setE(int $e)
    {
        $this->e = $e;
    }

    private function getF(): int
    {
        return $this->f;
    }

    private function setF(int $f)
    {
        $this->f = $f;
    }
}
