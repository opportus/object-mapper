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

use Opportus\ObjectMapper\Exception\CheckPointSeizingException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;

/**
 * The check point interface.
 *
 * @package Opportus\ObjectMapper\Point
 * @author Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface CheckPointInterface
{
    /**
     * Controls the subject.
     *
     * @param mixed $subject The value going to be assigned to the target point
     * @param RouteInterface $route The route which the check point is currently on
     * @param MapInterface $map The map which the route is currently on
     * @param SourceInterface $source The source which the subject comes from
     * @param TargetInterface $target The target which the subject goes to
     * @return mixed The value going to be assigned to the target point
     * @throws CheckPointSeizingException
     * @throws InvalidOperationException
     */
    public function control(
        $subject,
        RouteInterface $route,
        MapInterface $map,
        SourceInterface $source,
        TargetInterface $target
    );
}
