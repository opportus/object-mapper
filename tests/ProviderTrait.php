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

use Opportus\ObjectMapper\Point\CheckPointCollection;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\MethodDynamicSourcePoint;
use Opportus\ObjectMapper\Point\MethodParameterDynamicTargetPoint;
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Point\PropertyDynamicSourcePoint;
use Opportus\ObjectMapper\Point\PropertyDynamicTargetPoint;
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Route\Route;

/**
 * The provider trait.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
trait ProviderTrait
{
    public function provideSource(): array
    {
        return [
            [
                new ObjectA(),
            ],
            [
                new ObjectB(),
            ],
        ];
    }

    public function provideRoute(): array
    {
        $routes = [];

        foreach ($this->provideRoutePoints() as $routePoints) {
            $routes[] = [new Route(
                $routePoints[0],
                $routePoints[1],
                $routePoints[2]
            )];
        }

        return $routes;
    }

    public function provideRoutePoints(): array
    {
        return [
            [
                new PropertyStaticSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyStaticSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyStaticSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyStaticSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setZ',
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],

            [
                new MethodStaticSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodStaticSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodStaticSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodStaticSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setZ',
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],

            [
                new PropertyDynamicSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyDynamicSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyDynamicSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new PropertyDynamicSourcePoint(\sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setZ',
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],

            [
                new MethodDynamicSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodDynamicSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodDynamicSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
            [
                new MethodDynamicSourcePoint(\sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setZ',
                    'z'
                )),
                new CheckPointCollection([
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                    $this->getMockBuilder(CheckPointInterface::class)->getMock(),
                ]),
            ],
        ];
    }

    public function providePointsFqn(): array
    {
        $sourcePointFqns = $this->provideSourcePointFqn();
        $targetPointFqns = $this->provideTargetPointFqn();

        $pointsFqn = [];

        foreach ($sourcePointFqns as $key => $value) {
            $pointsFqn[$key][0] = $sourcePointFqns[$key][0];
            $pointsFqn[$key][1] = $targetPointFqns[$key][0];
        }

        return $pointsFqn;
    }

    public function provideSourcePoint(): array
    {
        return $this->provideStaticSourcePoint() +
            $this->provideDynamicSourcePoint();
    }

    public function provideSourcePointFqn(): array
    {
        return $this->provideStaticSourcePointFqn() +
            $this->provideDynamicSourcePointFqn();
    }

    public function provideInvalidSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
        ];
    }

    public function provideTargetPointFqn(): array
    {
        return $this->provideStaticTargetPointFqn() +
            $this->provideDynamicTargetPointFqn();
    }

    public function provideInvalidTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
        ];
    }

    public function provideStaticSourcePoint(): array
    {
        $pointFactory = new PointFactory();

        $points = [];

        foreach ($this->provideStaticSourcePointFqn() as $pointFqn) {
            $points[] = [$pointFactory->createStaticSourcePoint($pointFqn[0])];
        }

        return $points;
    }

    public function provideStaticSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectB::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectB::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectB::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectB::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectB::class,
                    'getF'
                ),
            ],
        ];
    }

    public function provideInvalidStaticSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
        ];
    }

    public function provideDynamicSourcePoint(): array
    {
        $pointFactory = new PointFactory();

        $points = [];

        foreach ($this->provideDynamicSourcePointFqn() as $pointFqn) {
            $points[] = [$pointFactory->createDynamicSourcePoint($pointFqn[0])];
        }

        return $points;
    }

    public function provideDynamicSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getY'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getY'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectB::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectB::class,
                    'getY'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectB::class,
                    'getY'
                ),
            ],
        ];
    }

    public function provideInvalidDynamicSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
        ];
    }

    public function provideStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$a',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectB::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$a',
                    ObjectB::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectB::class,
                    'setF',
                    'f'
                ),
            ],
        ];
    }

    public function provideInvalidStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
        ];
    }

    public function provideDynamicTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setY',
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$a',
                    ObjectA::class,
                    'setY',
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectB::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectB::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectB::class,
                    'setY',
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$a',
                    ObjectB::class,
                    'setY',
                    'y'
                ),
            ],
        ];
    }

    public function provideInvalidDynamicTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
        ];
    }

    public function providePropertyStaticSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
        ];
    }

    public function provideInvalidPropertyStaticSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    'NonObject',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'nonProperty'
                ),
            ],
        ];
    }

    public function providePropertyDynamicSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
        ];
    }

    public function provideInvalidPropertyDynamicSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    'NonObject',
                    'z'
                ),
            ],
        ];
    }

    public function provideMethodStaticSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getE'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getE'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getF'
                ),
            ],
        ];
    }

    public function provideInvalidMethodStaticSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getE'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    'NonObject',
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'nonMethod'
                ),
            ],
        ];
    }

    public function provideMethodDynamicSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
        ];
    }

    public function provideInvalidMethodDynamicSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '#%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    'NonObject',
                    'getZ'
                ),
            ],
        ];
    }

    public function providePropertyStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
        ];
    }

    public function provideInvalidPropertyStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    'NonObject',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'nonProperty'
                ),
            ],
        ];
    }

    public function providePropertyDynamicTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
        ];
    }

    public function provideInvalidPropertyDynamicTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '#%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    'NonObject',
                    'z'
                ),
            ],
        ];
    }
    public function provideMethodParameterStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setF',
                    'f'
                ),
            ],
        ];
    }

    public function provideInvalidMethodParameterStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    'NonObject',
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'nonMethod',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'nonParameter'
                ),
            ],
        ];
    }

    public function provideMethodParameterDynamicTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
        ];
    }

    public function provideInvalidMethodParameterDynamicTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    ObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    ObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    ObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    'NonObject',
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    ObjectA::class,
                    'setA',
                    'nonParameter'
                ),
            ],
        ];
    }
}
