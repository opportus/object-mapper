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
use \ReflectionClass;
use \stdClass;

/**
 * The test object trait.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
trait TestObjectTrait
{
    private $a;
    private $b;
    private $c;
    private $d;
    public $e;
    public $f;
    public $g;
    public $h;

    public function getA()
    {
        return $this->a;
    }

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getB()
    {
        return $this->b;
    }

    public function setB($b)
    {
        $this->b = $b;
    }

    public function getC()
    {
        return $this->c;
    }

    public function setC($c)
    {
        $this->c = $c;
    }

    public function getD()
    {
        return $this->d;
    }

    public function setD($d)
    {
        $this->d = $d;
    }

    public function get($arg)
    {
    }

    public function __call(string $dynamicMethodName, array $arguments)
    {
        if (\preg_match('/^get([A-Z]1)$/', $dynamicMethodName, $matches)) {
            $property = \strtolower($matches[1]);

            return $this->{$property};
        } elseif (\preg_match('/^set([A-Z]1)$/', $dynamicMethodName, $matches)) {
            $property = \strtolower($matches[1]);

            $this->{$property} = $arguments[0];
        }
    }

    private function initialize(
        $a = null,
        $b = null,
        $c = null,
        $d = null,
        $e = null,
        $f = null,
        $g = null,
        $h = null
    ) {
        $class = new ReflectionClass(self::class);

        foreach ($class->getProperties() as $property) {
            $staticPropertyName = $property->getName();
            $dynamicPropertyName = \sprintf('%s1', $property->getName());

            if (isset(${$staticPropertyName})) {
                $value = ${$staticPropertyName};
            } else {
                $value = 0;
            }

            $this->{$staticPropertyName} = $value;
            $this->{$dynamicPropertyName} = $value;
        }
    }
}
