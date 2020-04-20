<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinding;

use Opportus\ObjectMapper\Route\RouteCollection;
use Opportus\ObjectMapper\Source;
use Opportus\ObjectMapper\Target;

/**
 * The no path finding.
 *
 * @package Opportus\ObjectMapper\PathFinding
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class NoPathFinding implements PathFindingInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRoutes(Source $source, Target $target): RouteCollection
    {
        return new RouteCollection();
    }
}