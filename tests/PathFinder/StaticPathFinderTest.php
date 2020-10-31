<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\PathFinder;

use Opportus\ObjectMapper\PathFinder\PathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\PathFinder\StaticPathFinder;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\ObjectA;
use Opportus\ObjectMapper\Tests\ObjectB;
use PHPUnit\Framework\TestCase;

/**
 * The static path finder test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class StaticPathFinderTest extends TestCase
{
    public function testConstruct(): void
    {
        $pathFinder = $this->buildPathFinder();

        static::assertInstanceOf(PathFinderInterface::class, $pathFinder);
        static::assertInstanceOf(PathFinder::class, $pathFinder);
    }

    public function testGetRoutes(): void
    {
        $pathFinder = $this->buildPathFinder();

        $source = new Source(new ObjectA());
        $target = new Target(ObjectB::class);

        $routes = $pathFinder->getRoutes($source, $target);

        $expectedRoutes = $this->getExpectedRoutes();

        static::assertEquals($expectedRoutes, $routes);
    }

    private function buildPathFinder(): StaticPathFinder
    {
        return new StaticPathFinder(
            new RouteBuilder(new PointFactory())
        );
    }

    private function getExpectedRoutes(): RouteCollection
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        $routes = [];

        $routes[0] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '%s::getA()',
                ObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '%s::__construct()::$a',
                ObjectB::class
            ))
            ->getRoute();

        $routes[1] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '%s::getB()',
                ObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '%s::setB()::$b',
                ObjectB::class
            ))
            ->getRoute();

        $routes[2] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '%s::getC()',
                ObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '%s::setC()::$c',
                ObjectB::class
            ))
            ->getRoute();

        $routes[3] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '%s::getD()',
                ObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '%s::setD()::$d',
                ObjectB::class
            ))
            ->getRoute();

        $routes[4] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '%s::$f',
                ObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '%s::$f',
                ObjectB::class
            ))
            ->getRoute();

        $routes[5] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '%s::getG()',
                ObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '%s::$g',
                ObjectB::class
            ))
            ->getRoute();

        $routes[6] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '%s::$h',
                ObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '%s::setH()::$h',
                ObjectB::class
            ))
            ->getRoute();

        $routes[7] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '%s::$i',
                ObjectA::class
            ))
            ->setTargetPoint(\sprintf(
                '%s::$i',
                ObjectB::class
            ))
            ->getRoute();

        return new RouteCollection($routes);
    }
}
