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

use Exception;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\PathFinder\PathFinder;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\Tests\TestObjectA;
use Opportus\ObjectMapper\Tests\TestObjectB;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

/**
 * The path finder test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PathFinderTest extends TestCase
{
    public function testGetRoutesFirstException(): void
    {
        $source = new Source(new TestObjectA());
        $target = new Target(new TestObjectB());

        $pathFinder = $this->getMockForAbstractClass(
            PathFinder::class,
            [new RouteBuilder(new PointFactory())]
        );

        $pathFinder->method('getReferencePoints')
            ->with($source, $target)
            ->willThrowException(new Exception());

        $this->expectException(InvalidOperationException::class);

        $pathFinder->getRoutes($source, $target);
    }

    public function testGetRoutesSecondException(): void
    {
        $source = new Source(new TestObjectA());
        $target = new Target(new TestObjectB());

        $pathFinder = $this->getMockForAbstractClass(
            PathFinder::class,
            [new RouteBuilder(new PointFactory())]
        );

        $pathFinder->method('getReferencePoints')
            ->with($source, $target)
            ->willReturn([1]);

        $pathFinder->method('getReferencePointRoute')
            ->willThrowException(new Exception());

        $this->expectException(InvalidOperationException::class);

        $pathFinder->getRoutes($source, $target);
    }

    public function testGetReferencePointRouteException(): void
    {
        $pathFinder = $this->getMockBuilder(PathFinder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pathFinderReflection = new ReflectionClass($pathFinder);
        $pathFinderTestMethodReflection = $pathFinderReflection
            ->getMethod('getPointFqnFromReflection');

        $pathFinderTestMethodReflection->setAccessible(true);

        $this->expectException(InvalidArgumentException::class);

        $pathFinderTestMethodReflection->invokeArgs(
            $pathFinder,
            [new ReflectionClass(stdClass::class)]
        );
    }
}
