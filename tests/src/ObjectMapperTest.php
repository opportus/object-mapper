<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src;

use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;
use Opportus\ObjectMapper\ObjectMapper;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The object mapper test.
 *
 * Temporary test waiting lower level unit tests.
 *
 * @package Opportus\ObjectMapper\Tests\Src
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTest extends FinalBypassTestCase
{
    public function testMap(): void
    {
        $pointFactory = new PointFactory();
        $routeBuilder = new RouteBuilder($pointFactory);
        $mapBuilder = new MapBuilder($routeBuilder);
        $objectMapper = new ObjectMapper($mapBuilder);

        $target = $objectMapper->map($this->buildSource(), ObjectMapperTestClass::class);

        $this->assertEquals(1, $target->getA());
        $this->assertEquals(11, $target->getB());
    }

    private function buildSource(): ObjectMapperTestClass
    {
        $source = new ObjectMapperTestClass(1);
        $source->setB(11);

        return $source;
    }
}

/**
 * The object mapper test class.
 *
 * @package Opportus\ObjectMapper\Tests\Src
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTestClass
{
    private $a;
    private $b;

    public function __construct(int $a)
    {
        $this->a = $a;
    }

    public function getA(): int
    {
        return $this->a;
    }

    public function getB(): int
    {
        return $this->b;
    }

    public function setB(int $b)
    {
        $this->b = $b;
    }
}
