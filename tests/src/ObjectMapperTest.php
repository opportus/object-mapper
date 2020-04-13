<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src;

use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointCollection;
use Opportus\ObjectMapper\Map\Route\Point\CheckPointInterface;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;
use Opportus\ObjectMapper\Map\Route\Route;
use Opportus\ObjectMapper\Map\Route\RouteBuilder;
use Opportus\ObjectMapper\ObjectMapper;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The object mapper test.
 *
 * Temporary test waiting for lower level unit tests.
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

        $map = $mapBuilder
            ->addRoute(
                \sprintf(
                    '%s.getA()',
                    ObjectMapperTestObjectClass::class
                ),
                \sprintf(
                    '%s.__construct().$a',
                    ObjectMapperTestObjectClass::class
                ),
                new CheckPointCollection(
                    [new ObjectMapperTestCheckPointClass()]
                )
            )->buildMap(true)
        ;

        $target = $objectMapper->map(
            $this->buildSource(),
            ObjectMapperTestObjectClass::class,
            $map
        );

        $this->assertEquals(2, $target->getA());
        $this->assertEquals(11, $target->getB());
    }

    private function buildSource(): ObjectMapperTestObjectClass
    {
        $source = new ObjectMapperTestObjectClass(1);
        $source->setB(11);

        return $source;
    }
}

/**
 * The object mapper test object class.
 *
 * @package Opportus\ObjectMapper\Tests\Src
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTestObjectClass
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
}

/**
 * The object mapper test check point class.
 *
 * @package Opportus\ObjectMapper\Tests\Src
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTestCheckPointClass implements CheckPointInterface
{
    public function control(
        $value,
        Route $route,
        Source $source,
        Target $target
    ) {
        return 2;
    }
}
