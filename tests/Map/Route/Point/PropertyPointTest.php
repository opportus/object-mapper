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

use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use PHPUnit\Framework\TestCase;

/**
 * The property point test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyPointTest extends TestCase
{
    private $privatePropertyToTest = 0;
    protected $protectedPropertyToTest = 1;
    public $publicPropertyToTest = 2;

    public function testPropertyPointConstruction(): void
    {
        foreach ($this->getPropertiesToTest() as $propertyName) {
            $fqn = \sprintf('%s.$%s', self::class, $propertyName);
            $propertyPoint = new PropertyPoint($fqn);

            $this->assertEquals($propertyName, $propertyPoint->getName());
            $this->assertEquals($fqn, $propertyPoint->getFqn());
            $this->assertEquals(self::class, $propertyPoint->getClassFqn());
        }
    }

    public function testPropertyPointValue(): void
    {
        $object = new self();

        foreach ($this->getPropertiesToTest() as $propertyValue => $propertyName) {
            $fqn = \sprintf('%s.$%s', self::class, $propertyName);
            $propertyPoint = new PropertyPoint($fqn);

            $this->assertEquals($propertyValue, $propertyPoint->getValue($object));

            $propertyPoint->setValue($object, $propertyName);

            $this->assertEquals($propertyName, $propertyPoint->getValue($object));
        }
    }

    public function getPropertiesToTest(): array
    {
        return [
            'privatePropertyToTest',
            'protectedPropertyToTest',
            'publicPropertyToTest',
        ];
    }
}
