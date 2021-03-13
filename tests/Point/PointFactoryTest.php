<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Tests\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Point\IterableRecursionCheckPoint;
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
use Opportus\ObjectMapper\Point\RecursionCheckPoint;
use Opportus\ObjectMapper\Tests\Test;
use Opportus\ObjectMapper\Tests\TestInvalidArgumentException;
use Opportus\ObjectMapper\Tests\TestObjectA;
use Opportus\ObjectMapper\Tests\TestObjectB;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * The point factory test.
 *
 * @package Opportus\ObjectMapper\Tests\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactoryTest extends Test
{
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
     * @dataProvider provideInvalidSourcePointFqn
     * @dataProvider provideInvalidStaticSourcePointFqn
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
     * @dataProvider provideInvalidTargetPointFqn
     * @dataProvider provideInvalidStaticTargetPointFqn
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
     * @dataProvider provideInvalidSourcePointFqn
     * @dataProvider provideInvalidDynamicSourcePointFqn
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
     * @dataProvider provideInvalidTargetPointFqn
     * @dataProvider provideInvalidDynamicTargetPointFqn
     */
    public function testCreateDynamicTargetPointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createDynamicTargetPoint($fqn);
    }

    /**
     * @dataProvider provideSourcePointFqn
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
     * @dataProvider provideInvalidSourcePointFqn
     */
    public function testCreateSourcePointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createSourcePoint($fqn);
    }

    /**
     * @dataProvider provideTargetPointFqn
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
     * @dataProvider provideInvalidTargetPointFqn
     */
    public function testCreateTargetPointException(string $fqn): void
    {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);

        $pointFactory->createTargetPoint($fqn);
    }

    /**
     * @depends testCreateSourcePoint
     *
     * @return void
     */
    public function testCreateRecursionCheckPoint(): void
    {
        foreach ($this->provideSourcePointFqn() as $targetSourcePointFqn) {
            $targetSourcePointFqn = $targetSourcePointFqn[0];

            if (false !== \strpos($targetSourcePointFqn, TestObjectB::class)) {
                continue;
            }

            $pointFactory1 = $this->createPointFactory();
            $pointFactory2 = $this->createPointFactory();

            $point1 = $pointFactory1->createRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $targetSourcePointFqn
            );

            $point2 = new RecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $pointFactory2->createSourcePoint($targetSourcePointFqn)
            );

            static::assertEquals($point2, $point1);
        }
    }

    /**
     * @dataProvider provideInvalidSourcePointFqn
     *
     * @return void
     */
    public function testCreateRecursionCheckPointFirstException(
        string $targetSourcePointFqn
    ): void {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);
        $pointFactory->createRecursionCheckPoint(
            TestObjectA::class,
            TestObjectB::class,
            $targetSourcePointFqn
        );
    }

    /**
     * @dataProvider provideSourcePointFqn
     *
     * @return void
     */
    public function testCreateRecursionCheckPointSecondException1(
        string $targetSourcePointFqn
    ): void {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);
        $pointFactory->createRecursionCheckPoint(
            'Test',
            TestObjectB::class,
            $targetSourcePointFqn
        );
    }

    /**
     * @dataProvider provideSourcePointFqn
     *
     * @return void
     */
    public function testCreateRecursionCheckPointSecondException2(
        string $targetSourcePointFqn
    ): void {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);
        $pointFactory->createRecursionCheckPoint(
            TestObjectA::class,
            'Test',
            $targetSourcePointFqn
        );
    }

    /**
     * @depends testCreateSourcePoint
     *
     * @return void
     */
    public function testCreateIterableRecursionCheckPoint(): void
    {
        foreach ($this->provideSourcePointFqn() as $iterableTargetSourcePointFqn) {
            $iterableTargetSourcePointFqn = $iterableTargetSourcePointFqn[0];

            if (false !== \strpos($iterableTargetSourcePointFqn, TestObjectB::class)) {
                continue;
            }

            $pointFactory1 = $this->createPointFactory();
            $pointFactory2 = $this->createPointFactory();

            $point1 = $pointFactory1->createIterableRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $iterableTargetSourcePointFqn
            );

            $point2 = new IterableRecursionCheckPoint(
                TestObjectA::class,
                TestObjectB::class,
                $pointFactory2->createSourcePoint($iterableTargetSourcePointFqn)
            );

            static::assertEquals($point2, $point1);
        }
    }

    /**
     * @dataProvider provideInvalidSourcePointFqn
     *
     * @return void
     */
    public function testCreateIterableRecursionCheckPointFirstException(
        string $targetSourcePointFqn
    ): void {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);
        $pointFactory->createIterableRecursionCheckPoint(
            TestObjectA::class,
            TestObjectB::class,
            $targetSourcePointFqn
        );
    }

    /**
     * @dataProvider provideSourcePointFqn
     *
     * @return void
     */
    public function testCreateIterableRecursionCheckPointSecondException1(
        string $targetSourcePointFqn
    ): void {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);
        $pointFactory->createIterableRecursionCheckPoint(
            'Test',
            TestObjectB::class,
            $targetSourcePointFqn
        );
    }

    /**
     * @dataProvider provideSourcePointFqn
     *
     * @return void
     */
    public function testCreateIterableRecursionCheckPointSecondException2(
        string $targetSourcePointFqn
    ): void {
        $pointFactory = $this->createPointFactory();

        $this->expectException(InvalidArgumentException::class);
        $pointFactory->createIterableRecursionCheckPoint(
            TestObjectA::class,
            'Test',
            $targetSourcePointFqn
        );
    }
}
