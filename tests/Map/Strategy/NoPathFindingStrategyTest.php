<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Map\Strategy;

use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Strategy\NoPathFindingStrategy;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The no path finding strategy test.
 *
 * @package Opportus\ObjectMapper\Tests\Map\Strategy
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class NoPathFindingStrategyTest extends FinalBypassTestCase
{
    public function testConstruct(): void
    {
        $strategy = new NoPathFindingStrategy();

        static::assertInstanceOf(
            NoPathFindingStrategy::class,
            $strategy
        );
        static::assertInstanceOf(
            PathFindingStrategyInterface::class,
            $strategy
        );
    }

    public function testGetRoutes(): void
    {
        $strategy = new NoPathFindingStrategy();
        $source = $this->buildSource();
        $target = $this->buildTarget();

        /**
         * @var Source $source
         * @var Target $target
         */
        $routes = $strategy->getRoutes($source, $target);

        static::assertInstanceOf(RouteCollection::class, $routes);
        static::assertCount(0, $routes);
    }

    private function buildSource()
    {
        return $this->getMockBuilder(Source::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function buildTarget()
    {
        return $this->getMockBuilder(Target::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
