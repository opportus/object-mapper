<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Route\Point;

use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use PHPUnit\Framework\TestCase;

/**
 * The method point test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class MethodPointTest extends TestCase
{
    public function testMethodPointConstruction(): void
    {
        foreach ($this->getMethodsToTest() as $methodName) {
            $fqn = \sprintf('%s.%s()', self::class, $methodName);
            $methodPoint = new MethodPoint($fqn);

            $this->assertEquals($methodName, $methodPoint->getName());
            $this->assertEquals($fqn, $methodPoint->getFqn());
            $this->assertEquals(self::class, $methodPoint->getClassFqn());
        }
    }

    public function testMethodPointValue(): void
    {
        $object = new self();

        foreach ($this->getMethodsToTest() as $methodValue => $methodName) {
            $fqn = \sprintf('%s.%s()', self::class, $methodName);
            $methodPoint = new MethodPoint($fqn);

            $this->assertEquals($methodValue, $methodPoint->getValue($object));
        }
    }

    public function getMethodsToTest(): array
    {
        return [
            'privateMethodToTest',
            'protectedMethodToTest',
            'publicMethodToTest',
        ];
    }

    private function privateMethodToTest(): int
    {
        return 0;
    }

    protected function protectedMethodToTest(): int
    {
        return 1;
    }

    public function publicMethodToTest(): int
    {
        return 2;
    }
}
