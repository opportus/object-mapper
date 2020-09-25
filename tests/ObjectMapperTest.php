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

use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\ObjectMapper;
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;

/**
 * The object mapper test.
 *
 * Temporary test waiting for lower level unit tests.
 *
 * @package Opportus\ObjectMapper\Tests
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
            ->getRouteBuilder()
                ->setStaticSourcePoint(\sprintf('%s.getA()', ObjectMapperTestObjectClass::class))
                ->setStaticTargetPoint(\sprintf('%s.__construct().$a', ObjectMapperTestObjectClass::class))
                ->addCheckPoint(new ObjectMapperTestCheckPointClass())
                ->addRouteToMapBuilder()
                ->setStaticSourcePoint(\sprintf('%s.getA()', ObjectMapperTestObjectClass::class))
                ->setStaticTargetPoint(\sprintf('%s.setB().$b', ObjectMapperTestObjectClass::class))
                ->addRouteToMapBuilder()
                ->setStaticSourcePoint(\sprintf('%s.getA()', ObjectMapperTestObjectClass::class))
                ->setDynamicTargetPoint(\sprintf('%s.$c', ObjectMapperTestObjectClass::class))
                ->addRouteToMapBuilder()
                ->setStaticSourcePoint(\sprintf('%s.getA()', ObjectMapperTestObjectClass::class))
                ->setDynamicTargetPoint(\sprintf('%s.setD().$d', ObjectMapperTestObjectClass::class))
                ->addRouteToMapBuilder()
                ->setStaticSourcePoint(\sprintf('%s.getB()', ObjectMapperTestObjectClass::class))
                ->setDynamicTargetPoint(\sprintf('%s.setD().$dp', ObjectMapperTestObjectClass::class))
                ->addRouteToMapBuilder()
                ->getMapBuilder()
            ->addStaticPathFinder()
            ->getMap();

        $target = $objectMapper->map(
            $this->buildSource(),
            ObjectMapperTestObjectClass::class,
            $map
        );

        static::assertEquals(2, $target->getA());
        static::assertEquals(1, $target->getB());
        static::assertEquals(1, $target->c);
        static::assertEquals(-10, $target->d);

        $map = $mapBuilder
            ->addPathFinder(new StaticPathFinder($routeBuilder))
            ->getMap();

        $target = $objectMapper->map(
            $this->buildSource(),
            ObjectMapperTestObjectClass::class,
            $map
        );

        static::assertEquals(1, $target->getA());
        static::assertEquals(11, $target->getB());

        $map = $mapBuilder
            ->addDynamicPathFinder()
            ->getMap();

        $target = $objectMapper->map(
            $this->buildSource(),
            DynamicObjectMapperTestObjectClass::class,
            $map
        );

        static::assertEquals(2, $target->e);
        static::assertEquals(3, $target->f);
    }

    /**
     * @return ObjectMapperTestObjectClass
     */
    private function buildSource(): ObjectMapperTestObjectClass
    {
        $source = new ObjectMapperTestObjectClass(1);
        $source->setB(11);
        $source->f = 3;

        return $source;
    }
}

/**
 * The object mapper test object class.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTestObjectClass
{
    private $a;
    private $b;
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

    public function getE()
    {
        return 2;
    }

    public function __get($propertyName)
    {
        if ($propertyName === 'c' && isset($this->c) || $propertyName === 'd' && isset($this->d)) {
            return $this->{$propertyName};
        }
    }

    public function __set($propertyName, $propertyValue)
    {
        if ($propertyName === 'c' || $propertyName === 'd') {
            $this->{$propertyName} = $propertyValue;
        }
    }

    public function __call($methodName, $arguments)
    {
        if ($methodName === 'setD') {
            $this->d = $arguments[0] - $arguments[1];
        }
    }
}

/**
 * The object mapper test dynamic object class.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class DynamicObjectMapperTestObjectClass
{
}

/**
 * The object mapper test check point class.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTestCheckPointClass implements CheckPointInterface
{
    public function control(
        $value,
        RouteInterface $route,
        MapInterface $map,
        Source $source,
        Target $target
    ) {
        return 2;
    }
}
