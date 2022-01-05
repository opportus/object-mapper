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

use Opportus\ObjectMapper\Exception\InvalidTargetOperationException;
use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\ObjectMapper;
use Opportus\ObjectMapper\ObjectMapperInterface;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;
use PHPUnit\Framework\TestCase;

/**
 * The object mapper test.
 *
 * Temporary test waiting for lower level unit tests.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTest extends TestCase
{
    public function testMap(): void
    {
        $map = $this->getMapBuilder()
            ->getRouteBuilder()
                ->setStaticSourcePoint(\sprintf('%s::getA()', ObjectMapperTestObjectClass::class))
                ->setStaticTargetPoint(\sprintf('%s::__construct()::$a', ObjectMapperTestObjectClass::class))
                ->addCheckPoint(new ObjectMapperTestCheckPointClass())
                ->addRouteToMapBuilder()

                ->setStaticSourcePoint(\sprintf('%s::getA()', ObjectMapperTestObjectClass::class))
                ->setStaticTargetPoint(\sprintf('%s::setB()::$b', ObjectMapperTestObjectClass::class))
                ->addRouteToMapBuilder()

                ->setStaticSourcePoint(\sprintf('%s::getA()', ObjectMapperTestObjectClass::class))
                ->setDynamicTargetPoint(\sprintf('%s::$c', ObjectMapperTestObjectClass::class))
                ->addRouteToMapBuilder()

                ->setStaticSourcePoint(\sprintf('%s::getA()', ObjectMapperTestObjectClass::class))
                ->setDynamicTargetPoint(\sprintf('%s::setD()::$d', ObjectMapperTestObjectClass::class))
                ->addRouteToMapBuilder()

                ->setStaticSourcePoint(\sprintf('%s::getB()', ObjectMapperTestObjectClass::class))
                ->setDynamicTargetPoint(\sprintf('%s::setD()::$dp', ObjectMapperTestObjectClass::class))
                ->addRouteToMapBuilder()

                ->getMapBuilder()
            ->addStaticPathFinder()
            ->getMap();

        $target = $this->getObjectMapper()->map(
            $this->buildSource(),
            ObjectMapperTestObjectClass::class,
            $map
        );

        static::assertEquals(2, $target->getA());
        static::assertEquals(1, $target->getB());
        static::assertEquals(1, $target->c);
        static::assertEquals(-10, $target->d);
        static::assertInstanceOf(ObjectMapperTestObjectBClass::class, $target->getG());
        static::assertEquals(1, $target->getG()->getA());
        static::assertIsArray($target->getH());
        static::assertContainsOnlyInstancesOf(ObjectMapperTestObjectBClass::class, $target->getH());
        foreach ($target->getH() as $v) {
            static::assertEquals(1, $v->getA());
        }
        static::assertIsBool($target->isI());
        static::assertEquals(false, $target->isI());

        $map = $this->getMapBuilder()
            ->addStaticSourceToDynamicTargetPathFinder()
            ->getMap();

        $target = $this->getObjectMapper()->map(
            $this->buildSource(),
            DynamicObjectMapperTestObjectClass::class,
            $map
        );

        static::assertEquals(2, $target->e);
        static::assertEquals(3, $target->f);
        static::assertIsBool($target->i);
        static::assertEquals(false, $target->i);

        $map = $this->getMapBuilder()
            ->addDynamicSourceToStaticTargetPathFinder()
            ->getMap();

        $dynamicSource = new DynamicObjectMapperTestObjectClass();
        $dynamicSource->b = 44;
        $dynamicSource->h = [];
        $dynamicSource->i = true;

        $target = $this->getObjectMapper()->map(
            $dynamicSource,
            ObjectMapperTestObjectClass::class,
            $map
        );

        static::assertEquals(44, $target->getB());
        static::assertIsArray($target->getH());
        static::assertEquals(true, $target->isI());

        $this->expectException(InvalidTargetOperationException::class);
        $this->getObjectMapper()->map(new ObjectMapperTestObjectCClass(), ObjectMapperTestObjectCClass::class);
    }

    /**
     * @return ObjectMapperTestObjectClass
     */
    private function buildSource(): ObjectMapperTestObjectClass
    {
        $source = new ObjectMapperTestObjectClass(1);
        $source->setB(11);
        $source->f = 3;
        $source->setH([
            new ObjectMapperTestObjectAClass(),
            new ObjectMapperTestObjectAClass(),
            new ObjectMapperTestObjectAClass()
        ]);
        $source->setI(false);

        return $source;
    }

    private function getRouteBuilder(): RouteBuilderInterface
    {
        return new RouteBuilder(new PointFactory());
    }

    private function getMapBuilder(): MapBuilderInterface
    {
        return new MapBuilder($this->getRouteBuilder());
    }

    private function getObjectMapper(): ObjectMapperInterface
    {
        return new ObjectMapper($this->getMapBuilder());
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
    private $g;
    private $h;
    private $i;

    public function __construct(int $a = 22)
    {
        $this->a = $a;
        $this->g = new ObjectMapperTestObjectAClass();
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

    public function setI(bool $i)
    {
        $this->i = $i;
    }

    public function getG()
    {
        return $this->g;
    }

    public function setG(ObjectMapperTestObjectBClass $g)
    {
        return $this->g = $g;
    }

    public function getH(): array
    {
        return $this->h;
    }

    /**
     * @param Opportus\ObjectMapper\Tests\ObjectMapperTestObjectBClass[] $h
     * @return array
     */
    public function setH(array $h)
    {
        return $this->h = $h;
    }

    public function isI()
    {
        return $this->i;
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
 * The object mapper test object A class.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTestObjectAClass
{
    private $a = 1;

    public function getA(): int
    {
        return $this->a;
    }

    public function setA(int $a)
    {
        $this->a = $a;
    }
}

/**
 * The object mapper test object B class.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTestObjectBClass
{
    private $a = 2;

    public function getA(): int
    {
        return $this->a;
    }

    public function setA(int $a)
    {
        $this->a = $a;
    }
}

/**
 * The object mapper test object C class.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperTestObjectCClass
{
    private $a = 3;

    public function getA(): int
    {
        return $this->a;
    }

    public function setA(int $a)
    {
        throw new \Exception();
    }
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
        SourceInterface $source,
        TargetInterface $target
    ) {
        return 2;
    }
}
