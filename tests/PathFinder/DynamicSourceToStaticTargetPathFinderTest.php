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

use Opportus\ObjectMapper\PathFinder\DynamicSourceToStaticTargetPathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\ObjectA;
use Opportus\ObjectMapper\Tests\ObjectB;
use PHPUnit\Framework\TestCase;

/**
 * The dynamic source to static target path finder test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class DynamicSourceToStaticTargetPathFinderTest extends TestCase
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

        $source = new ObjectB();
        $source->j = 1;
        $source->k = 1;
        $source->m = 1;
        $source->n = 1;

        $source = new Source($source);
        $target = new Target(ObjectA::class);

        $routes = $pathFinder->getRoutes($source, $target);

        $expectedRoutes = $this->getExpectedRoutes();

        static::assertEquals($expectedRoutes, $routes);
    }

    private function buildPathFinder(): DynamicSourceToStaticTargetPathFinder
    {
        return new DynamicSourceToStaticTargetPathFinder(
            new RouteBuilder(new PointFactory())
        );
    }

    private function getExpectedRoutes(): RouteCollection
    {
        $routeBuilder = new RouteBuilder(new PointFactory());

        $routes = [];

        $routes[0] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '~%s::$j',
                ObjectB::class
            ))
            ->setTargetPoint(\sprintf(
                '#%s::setJ()::$j',
                ObjectA::class
            ))
            ->getRoute();

        $routes[1] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '~%s::$m',
                ObjectB::class
            ))
            ->setTargetPoint(\sprintf(
                '#%s::setM()::$m',
                ObjectA::class
            ))
            ->getRoute();

        $routes[2] = $routeBuilder
            ->setSourcePoint(\sprintf(
                '~%s::$n',
                ObjectB::class
            ))
            ->setTargetPoint(\sprintf(
                '#%s::$n',
                ObjectA::class
            ))
            ->getRoute();

        return new RouteCollection($routes);
    }
}
