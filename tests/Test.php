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

use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\ObjectMapper;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use PHPUnit\Framework\TestCase;

/**
 * The test.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
abstract class Test extends TestCase
{
    use TestDataProviderTrait;

    protected function createPointFactory(): PointFactory
    {
        return new PointFactory();
    }

    protected function createRouteBuilder(): RouteBuilder
    {
        return new RouteBuilder(
            $this->createPointFactory()
        );
    }

    protected function createMapBuilder(): MapBuilder
    {
        return new MapBuilder(
            $this->createRouteBuilder()
        );
    }

    protected function createObjectMapper(): ObjectMapper
    {
        return new ObjectMapper(
            $this->createMapBuilder()
        );
    }
}
