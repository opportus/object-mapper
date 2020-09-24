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

/**
 * The target point.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
abstract class TargetPoint extends ObjectPoint implements TargetPointInterface
{
    /**
     * @var string $targetFqn
     */
    protected $targetFqn;

    /**
     * {@inheritdoc}
     */
    public function getTargetFqn(): string
    {
        return $this->targetFqn;
    }
}
