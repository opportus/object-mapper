<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Point\MethodDynamicSourcePoint;
use Opportus\ObjectMapper\Point\MethodParameterDynamicTargetPoint;
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use Opportus\ObjectMapper\Point\PointFactory;
use Opportus\ObjectMapper\Point\PointFactoryInterface;
use Opportus\ObjectMapper\Point\PropertyDynamicSourcePoint;
use Opportus\ObjectMapper\Point\PropertyDynamicTargetPoint;
use Opportus\ObjectMapper\Point\PropertyStaticSourcePoint;
use Opportus\ObjectMapper\Point\PropertyStaticTargetPoint;
use Opportus\ObjectMapper\Tests\InvalidArgumentException as TestInvalidArgumentException;
use Opportus\ObjectMapper\Tests\ObjectA;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * The point factory test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactoryTest extends TestCase
{
    private function createPointFactory(): PointFactoryInterface
    {
        return new PointFactory();
    }

    public function testConstruct(): void
    {
        $pointFactory = $this->createPointFactory();

        static::assertInstanceOf(PointFactory::class, $pointFactory);
        static::assertInstanceOf(PointFactoryInterface::class, $pointFactory);
    }

    /**
     * @dataProvider provideStaticSourcePointFqn
     */
    public function testCreateStaticSourcePoint(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $point = $pointFactory->createStaticSourcePoint($fqn);

        if (\preg_match('/^#?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(PropertyStaticSourcePoint::class, $point);

            return;
        }

        if (\preg_match('/^#?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/', $fqn)) {
            static::assertInstanceOf(MethodStaticSourcePoint::class, $point);

            return;
        }

        $message = \sprintf(
            'The argument must match either %s or %s FQN regex pattern.',
            PropertyStaticSourcePoint::class,
            MethodStaticSourcePoint::class
        );

        throw new TestInvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * @dataProvider provideSourcePointFqnException
     * @dataProvider provideStaticSourcePointFqnException
     */
    public function testCreateStaticSourcePointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createStaticSourcePoint($fqn);
    }

    /**
     * @dataProvider provideStaticTargetPointFqn
     */
    public function testCreateStaticTargetPoint(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $point = $pointFactory->createStaticTargetPoint($fqn);

        if (\preg_match('/^#?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(PropertyStaticTargetPoint::class, $point);

            return;
        }

        if (\preg_match('/^#?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(MethodParameterStaticTargetPoint::class, $point);

            return;
        }

        $message = \sprintf(
            'The argument must match either %s or %s FQN regex pattern.',
            PropertyStaticTargetPoint::class,
            MethodParameterStaticTargetPoint::class
        );

        throw new TestInvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * @dataProvider provideTargetPointFqnException
     * @dataProvider provideStaticTargetPointFqnException
     */
    public function testCreateStaticTargetPointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createStaticTargetPoint($fqn);
    }

    /**
     * @dataProvider provideDynamicSourcePointFqn
     */
    public function testCreateDynamicSourcePoint(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $point = $pointFactory->createDynamicSourcePoint($fqn);

        if (\preg_match('/^~?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(PropertyDynamicSourcePoint::class, $point);

            return;
        }

        if (\preg_match('/^~?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/', $fqn)) {
            static::assertInstanceOf(MethodDynamicSourcePoint::class, $point);

            return;
        }

        $message = \sprintf(
            'The argument must match either %s or %s FQN regex pattern.',
            PropertyDynamicSourcePoint::class,
            MethodDynamicSourcePoint::class
        );

        throw new TestInvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * @dataProvider provideSourcePointFqnException
     * @dataProvider provideDynamicSourcePointFqnException
     */
    public function testCreateDynamicSourcePointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createDynamicSourcePoint($fqn);
    }

    /**
     * @dataProvider provideDynamicTargetPointFqn
     */
    public function testCreateDynamicTargetPoint(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $point = $pointFactory->createDynamicTargetPoint($fqn);

        if (\preg_match('/^~?([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(PropertyDynamicTargetPoint::class, $point);

            return;
        } elseif (\preg_match('/^~?([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(MethodParameterDynamicTargetPoint::class, $point);

            return;
        }

        $message = \sprintf(
            'The argument must match either %s or %s FQN regex pattern.',
            PropertyDynamicTargetPoint::class,
            MethodParameterDynamicTargetPoint::class
        );

        throw new TestInvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * @dataProvider provideTargetPointFqnException
     * @dataProvider provideDynamicTargetPointFqnException
     */
    public function testCreateDynamicTargetPointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createDynamicTargetPoint($fqn);
    }

    /**
     * @dataProvider provideStaticSourcePointFqn
     * @dataProvider provideDynamicSourcePointFqn
     */
    public function testCreateSourcePoint(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $point = $pointFactory->createSourcePoint($fqn);

        if (\preg_match('/^#([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(PropertyStaticSourcePoint::class, $point);

            return;
        }

        if (\preg_match('/^#([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/', $fqn)) {
            static::assertInstanceOf(MethodStaticSourcePoint::class, $point);

            return;
        }

        if (\preg_match('/^~([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(PropertyDynamicSourcePoint::class, $point);

            return;
        }

        if (\preg_match('/^~([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/', $fqn)) {
            static::assertInstanceOf(MethodDynamicSourcePoint::class, $point);

            return;
        }

        if (\preg_match('/^([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            try {
                static::assertInstanceOf(PropertyStaticSourcePoint::class, $point);

                return;
            } catch (ExpectationFailedException $staticPointException) {
            }

            try {
                static::assertInstanceOf(PropertyDynamicSourcePoint::class, $point);

                return;
            } catch (ExpectationFailedException $dynamicPointException) {
            }

            throw new ExpectationFailedException(\sprintf(
                '%s%s%s',
                $staticPointException->getMessage(),
                \PHP_EOL,
                $dynamicPointException->getMessage()
            ));
        }

        if (\preg_match('/^([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)$/', $fqn)) {
            try {
                static::assertInstanceOf(MethodStaticSourcePoint::class, $point);

                return;
            } catch (ExpectationFailedException $staticPointException) {
            }

            try {
                static::assertInstanceOf(MethodDynamicSourcePoint::class, $point);

                return;
            } catch (ExpectationFailedException $dynamicPointException) {
            }

            throw new ExpectationFailedException(\sprintf(
                '%s%s%s',
                $staticPointException->getMessage(),
                \PHP_EOL,
                $dynamicPointException->getMessage()
            ));
        }

        $message = \sprintf(
            'The argument must match either %s or %s or %s or %s FQN regex pattern.',
            PropertyStaticSourcePoint::class,
            MethodStaticSourcePoint::class,
            PropertyDynamicSourcePoint::class,
            MethodDynamicSourcePoint::class
        );

        throw new TestInvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * @dataProvider provideSourcePointFqnException
     */
    public function testCreateSourcePointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createSourcePoint($fqn);
    }

    /**
     * @dataProvider provideStaticTargetPointFqn
     * @dataProvider provideDynamicTargetPointFqn
     */
    public function testCreateTargetPoint(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $point = $pointFactory->createTargetPoint($fqn);

        if (\preg_match('/^#([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(PropertyStaticTargetPoint::class, $point);

            return;
        }

        if (\preg_match('/^#([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(MethodParameterStaticTargetPoint::class, $point);

            return;
        }

        if (\preg_match('/^~([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(PropertyDynamicTargetPoint::class, $point);

            return;
        }

        if (\preg_match('/^~([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            static::assertInstanceOf(MethodParameterDynamicTargetPoint::class, $point);

            return;
        }

        if (\preg_match('/^([A-Za-z0-9\\\_]+)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            try {
                static::assertInstanceOf(PropertyStaticTargetPoint::class, $point);

                return;
            } catch (ExpectationFailedException $staticPointException) {
            }

            try {
                static::assertInstanceOf(PropertyDynamicTargetPoint::class, $point);

                return;
            } catch (ExpectationFailedException $dynamicPointException) {
            }

            throw new ExpectationFailedException(\sprintf(
                '%s%s%s',
                $staticPointException->getMessage(),
                \PHP_EOL,
                $dynamicPointException->getMessage()
            ));
        }

        if (\preg_match('/^([A-Za-z0-9\\\_]+)::([A-Za-z0-9_]+)\(\)::\$([A-Za-z0-9_]+)$/', $fqn)) {
            try {
                static::assertInstanceOf(MethodParameterStaticTargetPoint::class, $point);

                return;
            } catch (ExpectationFailedException $staticPointException) {
            }

            try {
                static::assertInstanceOf(MethodParameterDynamicTargetPoint::class, $point);

                return;
            } catch (ExpectationFailedException $dynamicPointException) {
            }

            throw new ExpectationFailedException(\sprintf(
                '%s%s%s',
                $staticPointException->getMessage(),
                \PHP_EOL,
                $dynamicPointException->getMessage()
            ));
        }

        $message = \sprintf(
            'The argument must match either %s or %s or %s or %s FQN regex pattern.',
            PropertyStaticTargetPoint::class,
            MethodParameterStaticTargetPoint::class,
            PropertyDynamicTargetPoint::class,
            MethodParameterDynamicTargetPoint::class
        );

        throw new TestInvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * @dataProvider provideTargetPointFqnException
     */
    public function testCreateTargetPointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createTargetPoint($fqn);
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
        ];
    }

    public function provideStaticSourcePointFqnException(): array
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
        ];
    }

    public function provideStaticTargetPointFqnException(): array
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
        ];
    }

    public function provideDynamicSourcePointFqnException(): array
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
        ];
    }

    public function provideDynamicTargetPointFqnException(): array
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

    public function provideSourcePointFqnException(): array
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

    public function provideTargetPointFqnException(): array
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
}
