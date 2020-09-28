<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The point factory.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactory implements PointFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createStaticSourcePoint(string $pointFqn): StaticSourcePointInterface
    {
        if (\preg_match(
            MethodStaticSourcePoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            return new MethodStaticSourcePoint($pointFqn);
        } elseif (\preg_match(
            PropertyStaticSourcePoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            return new PropertyStaticSourcePoint($pointFqn);
        }

        $message = \sprintf('%s is not a static source point FQN.', $pointFqn);

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function createStaticTargetPoint(string $pointFqn): StaticTargetPointInterface
    {
        if (\preg_match(
            MethodParameterStaticTargetPoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            return new MethodParameterStaticTargetPoint($pointFqn);
        } elseif (\preg_match(
            PropertyStaticTargetPoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            return new PropertyStaticTargetPoint($pointFqn);
        }

        $message = \sprintf('%s is not a static target point FQN.', $pointFqn);

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function createDynamicSourcePoint(string $pointFqn): DynamicSourcePointInterface
    {
        if (\preg_match(
            MethodDynamicSourcePoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            return new MethodDynamicSourcePoint($pointFqn);
        } elseif (\preg_match(
            PropertyDynamicSourcePoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            return new PropertyDynamicSourcePoint($pointFqn);
        }

        $message = \sprintf('%s is not a dynamic source point FQN.', $pointFqn);

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function createDynamicTargetPoint(string $pointFqn): DynamicTargetPointInterface
    {
        if (\preg_match(
            MethodParameterDynamicTargetPoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            return new MethodParameterDynamicTargetPoint($pointFqn);
        } elseif (\preg_match(
            PropertyDynamicTargetPoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            return new PropertyDynamicTargetPoint($pointFqn);
        }

        $message = \sprintf('%s is not a dynamic target point FQN.', $pointFqn);

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function createSourcePoint(string $pointFqn): SourcePointInterface
    {
        try {
            return $this->createStaticSourcePoint($pointFqn);
        } catch (InvalidArgumentException $staticPointException) {}

        try {
            return $this->createDynamicSourcePoint($pointFqn);
        } catch (InvalidArgumentException $dynamicPointException) {}

        $message = \sprintf(
            '%s%s%s',
            $staticPointException->getMessage(),
            \PHP_EOL,
            $dynamicPointException->getMessage()
        );

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function createTargetPoint(string $pointFqn): TargetPointInterface
    {
        try {
            return $this->createStaticTargetPoint($pointFqn);
        } catch (InvalidArgumentException $staticPointException) {}

        try {
            return $this->createDynamicTargetPoint($pointFqn);
        } catch (InvalidArgumentException $dynamicPointException) {}

        $message = \sprintf(
            '%s%s%s',
            $staticPointException->getMessage(),
            \PHP_EOL,
            $dynamicPointException->getMessage()
        );

        throw new InvalidArgumentException(1, __METHOD__, $message);
    }
}
