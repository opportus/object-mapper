<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Map\Route\Point;

use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use PHPUnit\Framework\TestCase;

/**
 * The parameter point test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ParameterPointTest extends TestCase
{
    public function testParameterPointConstruction(): void
    {
        foreach ($this->getParametersToTest() as $methodName => $parameterName) {
            $fqn = \sprintf('%s::%s()::$%s', self::class, $methodName, $parameterName);
            $parameterPoint = new ParameterPoint($fqn);

            $this->assertEquals($parameterName, $parameterPoint->getName());
            $this->assertEquals($fqn, $parameterPoint->getFqn());
            $this->assertEquals(self::class, $parameterPoint->getClassFqn());
            $this->assertEquals($methodName, $parameterPoint->getMethodName());
            $this->assertEquals(0, $parameterPoint->getPosition());
        }
    }

    public function getParametersToTest(): array
    {
        return [
            'privateMethodToTest' => 'parameter',
            'protectedMethodToTest' => 'parameter',
            'publicMethodToTest' => 'parameter',
        ];
    }

    private function privateMethodToTest($parameter): int
    {
        return 0;
    }

    protected function protectedMethodToTest($parameter): int
    {
        return 1;
    }

    public function publicMethodToTest($parameter): int
    {
        return 2;
    }
}
