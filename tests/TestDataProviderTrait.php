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
 * The test data provider trait.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
trait TestDataProviderTrait
{
    public function provideSource(): array
    {
        return [
            [
                new TestObjectA(),
            ],
            [
                new TestObjectB(),
            ],
        ];
    }

    public function provideTarget(): array
    {
        return [
            [
                new TestObjectA(),
            ],
            [
                new TestObjectB(),
            ],
            [
                TestObjectA::class,
            ],
            [
                TestObjectB::class,
            ],
        ];
    }

    public function provideInvalidTarget(): array
    {
        return [
            [
                'Test',
            ],
            [
                true,
            ],
            [
                false,
            ],
            [
                3,
            ],
            [
                3.14,
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
                    TestObjectA::class,
                    'a'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'a'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'a'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'a'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'getA'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'getA'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'getA'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'getA'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'z'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'z'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'z'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'z'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'getZ'
                )),
                new PropertyStaticTargetPoint(\sprintf(
                    '%s::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'getZ'
                )),
                new MethodParameterStaticTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'getZ'
                )),
                new PropertyDynamicTargetPoint(\sprintf(
                    '%s::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'getZ'
                )),
                new MethodParameterDynamicTargetPoint(\sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
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
        return \array_merge(
            $this->provideStaticSourcePoint(),
            $this->provideDynamicSourcePoint()
        );
    }

    public function provideSourcePointFqn(): array
    {
        return \array_merge(
            $this->provideStaticSourcePointFqn(),
            $this->provideDynamicSourcePointFqn()
        );
    }

    public function provideInvalidSourcePointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
        ];
    }

    public function provideTargetPoint(): array
    {
        return \array_merge(
            $this->provideStaticTargetPoint(),
            $this->provideDynamicTargetPoint()
        );
    }

    public function provideTargetPointFqn(): array
    {
        return \array_merge(
            $this->provideStaticTargetPointFqn(),
            $this->provideDynamicTargetPointFqn()
        );
    }

    public function provideInvalidTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectB::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectB::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectB::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectB::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectB::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getY'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectA::class,
                    'getY'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectB::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectB::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectB::class,
                    'getY'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
                    'getZ'
                ),
            ],
        ];
    }

    public function provideStaticTargetPoint(): array
    {
        $pointFactory = new PointFactory();

        $points = [];

        foreach ($this->provideStaticTargetPointFqn() as $pointFqn) {
            $points[] = [$pointFactory->createStaticTargetPoint($pointFqn[0])];
        }

        return $points;
    }

    public function provideStaticTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$a',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectB::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectB::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$a',
                    TestObjectB::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
        ];
    }

    public function provideDynamicTargetPoint(): array
    {
        $pointFactory = new PointFactory();

        $points = [];

        foreach ($this->provideDynamicTargetPointFqn() as $pointFqn) {
            $points[] = [$pointFactory->createDynamicTargetPoint($pointFqn[0])];
        }

        return $points;
    }

    public function provideDynamicTargetPointFqn(): array
    {
        return [
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setY',
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$a',
                    TestObjectA::class,
                    'setY',
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectB::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectB::class,
                    'y'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectB::class,
                    'setY',
                    'y'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$a',
                    TestObjectB::class,
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
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
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
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getE'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
                    'getE'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'getA'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectA::class,
                    'getE'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectA::class,
                    'getF'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'e'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
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
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '#%s::%s()::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'setA',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    TestObjectA::class,
                    'setE',
                    'e'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    TestObjectA::class,
                    'setF',
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'f'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'nonMethod',
                    'a'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '~%s::%s()::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'setZ',
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::$%s',
                    TestObjectA::class,
                    'z'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()',
                    TestObjectA::class,
                    'getZ'
                ),
            ],
            [
                \sprintf(
                    '%s::%s()::$%s',
                    TestObjectA::class,
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
                    TestObjectA::class,
                    'setA',
                    'nonParameter'
                ),
            ],
        ];
    }
}
