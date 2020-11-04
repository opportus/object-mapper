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
            try {
                return new MethodStaticSourcePoint($pointFqn);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(1, '', 0, $exception);
            }
        } elseif (\preg_match(
            PropertyStaticSourcePoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            try {
                return new PropertyStaticSourcePoint($pointFqn);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(1, '', 0, $exception);
            }
        }

        $message = \sprintf('%s is not a static source point FQN.', $pointFqn);

        throw new InvalidArgumentException(1, $message);
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
            try {
                return new MethodParameterStaticTargetPoint($pointFqn);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(1, '', 0, $exception);
            }
        } elseif (\preg_match(
            PropertyStaticTargetPoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            try {
                return new PropertyStaticTargetPoint($pointFqn);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(1, '', 0, $exception);
            }
        }

        $message = \sprintf('%s is not a static target point FQN.', $pointFqn);

        throw new InvalidArgumentException(1, $message);
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
            try {
                return new MethodDynamicSourcePoint($pointFqn);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(1, '', 0, $exception);
            }
        } elseif (\preg_match(
            PropertyDynamicSourcePoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            try {
                return new PropertyDynamicSourcePoint($pointFqn);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(1, '', 0, $exception);
            }
        }

        $message = \sprintf('%s is not a dynamic source point FQN.', $pointFqn);

        throw new InvalidArgumentException(1, $message);
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
            try {
                return new MethodParameterDynamicTargetPoint($pointFqn);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(1, '', 0, $exception);
            }
        } elseif (\preg_match(
            PropertyDynamicTargetPoint::getFqnRegexPattern(),
            $pointFqn
        )) {
            try {
                return new PropertyDynamicTargetPoint($pointFqn);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(1, '', 0, $exception);
            }
        }

        $message = \sprintf('%s is not a dynamic target point FQN.', $pointFqn);

        throw new InvalidArgumentException(1, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function createSourcePoint(string $pointFqn): SourcePointInterface
    {
        try {
            return $this->createStaticSourcePoint($pointFqn);
        } catch (InvalidArgumentException $exception) {
        }

        try {
            return $this->createDynamicSourcePoint($pointFqn);
        } catch (InvalidArgumentException $exception) {
        }

        throw new InvalidArgumentException(1, '', 0, $exception);
    }

    /**
     * {@inheritdoc}
     */
    public function createTargetPoint(string $pointFqn): TargetPointInterface
    {
        try {
            return $this->createStaticTargetPoint($pointFqn);
        } catch (InvalidArgumentException $exception) {
        }

        try {
            return $this->createDynamicTargetPoint($pointFqn);
        } catch (InvalidArgumentException $exception) {
        }

        throw new InvalidArgumentException(1, '', 0, $exception);
    }

    /**
     * {@inheritdoc}
     */
    public function createRecursionCheckPoint(
        string $sourceFqn,
        string $targetFqn,
        string $targetSourcePointFqn
    ): RecursionCheckPoint {
        try {
            $targetSourcePoint = $this->createSourcePoint(
                $targetSourcePointFqn
            );
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(1, '', 0, $exception);
        }

        try {
            return new RecursionCheckPoint(
                $sourceFqn,
                $targetFqn,
                $targetSourcePoint
            );
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                $exception->getArgument(),
                '',
                0,
                $exception
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createIterableRecursionCheckPoint(
        string $sourceFqn,
        string $targetFqn,
        string $targetIterableSourcePointFqn
    ): IterableRecursionCheckPoint {
        try {
            $targetIterableSourcePoint = $this->createSourcePoint(
                $targetIterableSourcePointFqn
            );
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(1, '', 0, $exception);
        }

        try {
            return new IterableRecursionCheckPoint(
                $sourceFqn,
                $targetFqn,
                $targetIterableSourcePoint
            );
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                $exception->getArgument(),
                '',
                0,
                $exception
            );
        }
    }
}
