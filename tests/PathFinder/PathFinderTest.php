<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\PathFinder;

use Exception;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\PathFinder\PathFinder;
use Opportus\ObjectMapper\PathFinder\PathFinderInterface;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\RouteBuilder;
use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\Target;
use Opportus\ObjectMapper\TargetInterface;
use Opportus\ObjectMapper\Tests\Test;
use ReflectionClass;
use stdClass;

/**
 * The path finder test.
 *
 * @package Opportus\ObjectMapper\Tests\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
abstract class PathFinderTest extends Test
{
    public function testConstruct(): void
    {
        $pathFinder = $this->createPathFinder();

        static::assertInstanceOf(PathFinderInterface::class, $pathFinder);
        static::assertInstanceOf(PathFinder::class, $pathFinder);
    }

    /**
     * @dataProvider provideObjects
     */
    public function testGetRoutes(object $providedSource, $providedTarget): void
    {
        $pathFinder = $this->createPathFinder();

        $source = new Source($providedSource);
        $target = new Target($providedTarget);

        $expectedRoutes = [];

        $referencePoints = $this->getReferencePoints($source, $target);

        foreach ($referencePoints as $referencePoint) {
            $expectedRoute = $this->getReferencePointRoute(
                $source,
                $target,
                $referencePoint
            );

            if (null === $expectedRoute) {
                continue;
            }

            $expectedRoutes[] = $expectedRoute;
        }

        $routes = $pathFinder->getRoutes($source, $target);

        static::assertEquals(new RouteCollection($expectedRoutes), $routes);
    }

    /**
     * @dataProvider provideObjects
     */
    public function testGetRoutesFirstException(
        object $providedSource,
        $providedTarget
    ): void {
        $source = new Source($providedSource);
        $target = new Target($providedTarget);

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

    /**
     * @dataProvider provideObjects
     */
    public function testGetRoutesSecondException(
        object $providedSource,
        $providedTarget
    ): void {
        $source = new Source($providedSource);
        $target = new Target($providedTarget);

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

    /**
     * @dataProvider provideObjects
     */
    public function testGetReferencePointRouteException(
        object $providedSource,
        $providedTarget
    ): void {
        $pathFinder = $this->createPathFinder();

        $source = new Source($providedSource);
        $target = new Target($providedTarget);

        $pathFinderReflection = new ReflectionClass($pathFinder);
        $pathFinderTestMethodReflection = $pathFinderReflection
            ->getMethod('getReferencePointRoute');
        
        $pathFinderTestMethodReflection->setAccessible(true);

        $this->expectException(InvalidArgumentException::class);

        $pathFinderTestMethodReflection->invokeArgs(
            $pathFinder,
            [
                $source,
                $target,
                'test',
            ]
        );
    }

    public function testGetPointFqnFromReflection(): void
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

    abstract protected function getReferencePoints(
        SourceInterface $source,
        TargetInterface $target
    ): array;

    abstract protected function getReferencePointRoute(
        SourceInterface $source,
        TargetInterface $target,
        $referencePoint
    ): ?RouteInterface;

    abstract protected function createPathFinder(): PathFinder;
}
