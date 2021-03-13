<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Point;

/**
 * The object point.
 *
 * @package Opportus\ObjectMapper\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
abstract class ObjectPoint implements ObjectPointInterface
{
    /**
     * @var string $fqn
     */
    protected $fqn;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * {@inheritdoc}
     */
    abstract public static function getFqnRegexPattern(): string;

    /**
     * {@inheritdoc}
     */
    public function getFqn(): string
    {
        return $this->fqn;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
