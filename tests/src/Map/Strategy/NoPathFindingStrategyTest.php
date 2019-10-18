<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Src\Map\Strategy;

use Opportus\ObjectMapper\Context;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Strategy\NoPathFindingStrategy;
use Opportus\ObjectMapper\Map\Strategy\PathFindingStrategyInterface;
use Opportus\ObjectMapper\Tests\FinalBypassTestCase;

/**
 * The no path finding strategy test.
 *
 * @package Opportus\ObjectMapper\Tests\Src\Map\Strategy
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class NoPathFindingStrategyTest extends FinalBypassTestCase
{
    public function testConstruct(): void
    {
        $strategy = new NoPathFindingStrategy();

        $this->assertInstanceOf(NoPathFindingStrategy::class, $strategy);
        $this->assertInstanceOf(PathFindingStrategyInterface::class, $strategy);
    }

    public function testGetRoutes(): void
    {
        $strategy = new NoPathFindingStrategy();
        $context = $this->buildContext();

        $routes = $strategy->getRoutes($context);

        $this->assertInstanceOf(RouteCollection::class, $routes);
        $this->assertCount(0, $routes);
    }

    private function buildContext(): Context
    {
        return $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }
}
