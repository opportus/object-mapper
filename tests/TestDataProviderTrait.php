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
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Route\Route;
use ReflectionClass;

/**
 * The test data provider trait.
 *
 * @package Opportus\ObjectMapper\Tests
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
trait TestDataProviderTrait
{
    public function provideObjects(): array
    {
        $sources = $this->provideSource();
        $targets = $this->provideTarget();

        $objects = [];

        foreach ($sources as $key=> $source) {
            $objects[$key][0] = $source[0];
            $objects[$key][1] = $targets[$key][0];
        }

        return $objects;
    }

    public function provideSource(): array
    {
        return [
            [
                new TestObjectA(),
            ],
            [
                new TestObjectB(),
            ],
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
                new TestObjectB(),
            ],
            [
                new TestObjectA(),
            ],
            [
                TestObjectB::class,
            ],
            [
                TestObjectA::class,
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

    /**************************************************************************
     *
     * POINTS
     *
     *************************************************************************/

    public function provideRoutePoints(): array
    {
        $sourcePoints = $this->provideSourcePoint();
        $targetPoints = $this->provideTargetPoint();

        $points = [];

        foreach ($sourcePoints as $key => $sourcePoint) {
            $points[$key][0] = $sourcePoint[0];
            $points[$key][1] = $targetPoints[$key][0];

            if (0 === $key% 2) {
                $points[$key][2] = $this->createCheckPointCollection();
            } else {
                $points[$key][2] = $this->createEmptyCheckPointCollection();
            }
        }

        return $points;
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

    /**************************************************************************
     *
     * SOURCE POINTS
     *
     *************************************************************************/

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
        $invalidPointFqn = [];

        foreach ($this->provideSourcePointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    /**************************************************************************
     *
     * STATIC SOURCE POINTS
     *
     *************************************************************************/

    public function provideStaticSourcePoint(): array
    {
        $points = [];

        foreach ($this->provideStaticSourcePointFqn() as $pointFqn) {
            $points[] = [
                $this->createPointFactory()
                    ->createStaticSourcePoint($pointFqn[0])
            ];
        }

        return $points;
    }

    public function provideStaticSourcePointFqn(): array
    {
        return \array_merge(
            $this->providePropertyStaticSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn()
        );
    }

    public function provideInvalidStaticSourcePointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->provideStaticSourcePointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    public function providePropertyStaticSourcePointFqn(): array
    {
        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        $points = [];

        foreach ($classes as $class) {
            foreach ($class->getProperties() as $property) {
                $points[] = [\sprintf(
                    '#%s::$%s',
                    $property->getDeclaringClass()->getName(),
                    $property->getName()
                )];
            }
        }

        return $points;
    }

    public function provideInvalidPropertyStaticSourcePointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->providePropertyStaticSourcePointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    public function provideMethodStaticSourcePointFqn(): array
    {
        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        $points = [];

        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if (0 !== \strpos($method->getName(), 'get')) {
                    continue;
                }

                if (0 !== $method->getNumberOfRequiredParameters()) {
                    continue;
                }

                $points[] = [\sprintf(
                    '#%s::%s()',
                    $method->getDeclaringClass()->getName(),
                    $method->getName()
                )];
            }
        }

        return $points;
    }

    public function provideInvalidMethodStaticSourcePointFqn(): array
    {
        $invalidPointFqn = [];

        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if (0 !== \strpos($method->getName(), 'get')) {
                    continue;
                }

                if (0 === $method->getNumberOfRequiredParameters()) {
                    continue;
                }

                $invalidPointFqn[] = [\sprintf(
                    '#%s::%s()',
                    $method->getDeclaringClass()->getName(),
                    $method->getName()
                )];
            }
        }

        foreach ($this->provideMethodStaticSourcePointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyStaticSourcePointFqn(),
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyStaticTargetPointFqn(),
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    /**************************************************************************
     *
     * DYNAMIC SOURCE POINTS
     *
     *************************************************************************/

    public function provideDynamicSourcePoint(): array
    {
        $points = [];

        foreach ($this->provideDynamicSourcePointFqn() as $pointFqn) {
            $points[] = [
                $this->createPointFactory()
                    ->createDynamicSourcePoint($pointFqn[0])
            ];
        }

        return $points;
    }

    public function provideDynamicSourcePointFqn(): array
    {
        return \array_merge(
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn()
        );
    }

    public function provideInvalidDynamicSourcePointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->provideDynamicSourcePointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];

            $invalidPointFqn[] = [\str_replace('1', '', $pointFqn)];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyStaticSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->providePropertyStaticTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    public function providePropertyDynamicSourcePointFqn(): array
    {
        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        $points = [];

        foreach ($classes as $class) {
            foreach ($class->getProperties() as $property) {
                $points[] = [\sprintf(
                    '~%s::$%s1',
                    $property->getDeclaringClass()->getName(),
                    $property->getName()
                )];
            }
        }

        return $points;
    }

    public function provideInvalidPropertyDynamicSourcePointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->providePropertyDynamicSourcePointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];

            $invalidPointFqn[] = [\str_replace('1', '', $pointFqn)];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyStaticSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyStaticTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    public function provideMethodDynamicSourcePointFqn(): array
    {
        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        $points = [];

        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if (0 !== \strpos($method->getName(), 'get')) {
                    continue;
                }

                if (0 !== $method->getNumberOfRequiredParameters()) {
                    continue;
                }

                $points[] = [\sprintf(
                    '~%s::%s1()',
                    $method->getDeclaringClass()->getName(),
                    $method->getName()
                )];
            }
        }

        return $points;
    }

    public function provideInvalidMethodDynamicSourcePointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->provideMethodDynamicSourcePointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];

            $invalidPointFqn[] = [\str_replace('1', '', $pointFqn)];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyStaticSourcePointFqn(),
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->providePropertyStaticTargetPointFqn(),
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    /**************************************************************************
     *
     * TARGET POINTS
     *
     *************************************************************************/

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
        $invalidPointFqn = [];

        foreach ($this->provideTargetPointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn()
        );
    }

    /**************************************************************************
     *
     * STATIC TARGET POINTS
     *
     *************************************************************************/

    public function provideStaticTargetPoint(): array
    {
        $points = [];

        foreach ($this->provideStaticTargetPointFqn() as $pointFqn) {
            $points[] = [
                $this->createPointFactory()
                    ->createStaticTargetPoint($pointFqn[0])
            ];
        }

        return $points;
    }

    public function provideStaticTargetPointFqn(): array
    {
        return \array_merge(
            $this->providePropertyStaticTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
        );
    }

    public function provideInvalidStaticTargetPointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->provideStaticTargetPointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    public function providePropertyStaticTargetPointFqn(): array
    {
        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        $points = [];

        foreach ($classes as $class) {
            foreach ($class->getProperties() as $property) {
                $points[] = [\sprintf(
                    '#%s::$%s',
                    $property->getDeclaringClass()->getName(),
                    $property->getName()
                )];
            }
        }

        return $points;
    }

    public function provideInvalidPropertyStaticTargetPointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->providePropertyStaticTargetPointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    public function provideMethodParameterStaticTargetPointFqn(): array
    {
        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        $points = [];

        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if (
                    0 !== \strpos($method->getName(), 'set') &&
                    $method->getName() !== '__construct'
                ) {
                    continue;
                }

                foreach ($method->getParameters() as $parameter) {
                    $points[] = [\sprintf(
                        '#%s::%s()::$%s',
                        $parameter->getDeclaringClass()->getName(),
                        $parameter->getDeclaringFunction()->getName(),
                        $parameter->getName()
                    )];
                }
            }
        }

        return $points;
    }

    public function provideInvalidMethodParameterStaticTargetPointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->provideMethodParameterStaticTargetPointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyStaticSourcePointFqn(),
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyStaticTargetPointFqn(),
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    /**************************************************************************
     *
     * DYNAMIC TARGET POINTS
     *
     *************************************************************************/

    public function provideDynamicTargetPoint(): array
    {
        $points = [];

        foreach ($this->provideDynamicTargetPointFqn() as $pointFqn) {
            $points[] = [
                $this->createPointFactory()
                    ->createDynamicTargetPoint($pointFqn[0])
            ];
        }

        return $points;
    }

    public function provideDynamicTargetPointFqn(): array
    {
        return \array_merge(
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    public function provideInvalidDynamicTargetPointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->provideDynamicTargetPointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];

            $invalidPointFqn[] = [\str_replace('1', '', $pointFqn)];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyStaticSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyStaticTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn()
        );
    }

    public function providePropertyDynamicTargetPointFqn(): array
    {
        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        $points = [];

        foreach ($classes as $class) {
            foreach ($class->getProperties() as $property) {
                $points[] = [\sprintf(
                    '~%s::$%s1',
                    $property->getDeclaringClass()->getName(),
                    $property->getName()
                )];
            }
        }

        return $points;
    }

    public function provideInvalidPropertyDynamicTargetPointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->providePropertyDynamicTargetPointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];

            $invalidPointFqn[] = [\str_replace('1', '', $pointFqn)];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyStaticSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyStaticTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn(),
            $this->provideMethodParameterDynamicTargetPointFqn()
        );
    }

    public function provideMethodParameterDynamicTargetPointFqn(): array
    {
        $classes = [
            new ReflectionClass(TestObjectA::class),
            new ReflectionClass(TestObjectB::class),
        ];

        $points = [];

        foreach ($classes as $class) {
            foreach ($class->getMethods() as $method) {
                if (0 !== \strpos($method->getName(), 'set')) {
                    continue;
                }

                foreach ($method->getParameters() as $parameter) {
                    $points[] = [\sprintf(
                        '~%s::%s1()::$%s1',
                        $parameter->getDeclaringClass()->getName(),
                        $parameter->getDeclaringFunction()->getName(),
                        $parameter->getName()
                    )];
                }
            }
        }

        return $points;
    }

    public function provideInvalidMethodParameterDynamicTargetPointFqn(): array
    {
        $invalidPointFqn = [];

        foreach ($this->provideMethodParameterDynamicTargetPointFqn() as $pointFqn) {
            $pointFqn = $pointFqn[0];

            $invalidPointFqn[] = [\preg_replace(
                '/^(#|~)[A-Za-z0-9\\\_]+/',
                '$1Object',
                $pointFqn
            )];

            $invalidPointFqn[] = [\str_replace('1', '', $pointFqn)];
        }

        return \array_merge(
            $invalidPointFqn,
            $this->providePropertyStaticSourcePointFqn(),
            $this->providePropertyDynamicSourcePointFqn(),
            $this->provideMethodStaticSourcePointFqn(),
            $this->provideMethodDynamicSourcePointFqn(),
            $this->providePropertyStaticTargetPointFqn(),
            $this->providePropertyDynamicTargetPointFqn(),
            $this->provideMethodParameterStaticTargetPointFqn()
        );
    }

    private function createPointFactory(): PointFactory
    {
        return new PointFactory();
    }

    private function createCheckPointCollection(): CheckPointCollection
    {
        return new CheckPointCollection([
            $this->getMockBuilder(CheckPointInterface::class)->getMock(),
            $this->getMockBuilder(CheckPointInterface::class)->getMock(),
            $this->getMockBuilder(CheckPointInterface::class)->getMock(),
        ]);
    }

    private function createEmptyCheckPointCollection(): CheckPointCollection
    {
        return new CheckPointCollection();
    }
}
