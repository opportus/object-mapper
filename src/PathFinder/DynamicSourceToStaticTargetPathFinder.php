<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\PathFinder;

use Opportus\ObjectMapper\Route\RouteInterface;
use Opportus\ObjectMapper\SourceInterface;
use Opportus\ObjectMapper\TargetInterface;

/**
 * The default dynamic source to static target path finder.
 *
 * This behavior consists of guessing the dynamic point of the source to
 * connect to each static point of the target following the rules below.
 *
 * A target point can be:
 *
 * - A public property (`PropertyStaticTargetPoint`)
 * - A parameter of a public setter or a public constructor
 *   (`ParameterStaticTargetPoint`)
 *
 * The connectable source point can be:
 *
 * - A dynamically defined property having for name the same as the target
 *   point (`PropertyDynamicSourcePoint`)
 *
 * @package Opportus\ObjectMapper\PathFinder
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class DynamicSourceToStaticTargetPathFinder extends StaticPathFinder
{
    /**
     * {@inheritdoc}
     */
    protected function getReferencePointRoute(
        SourceInterface $source,
        TargetInterface $target,
        $referencePoint
    ): ?RouteInterface {
        $targetPointReflection = $referencePoint;
        $sourceClassReflection = $source->getClassReflection();
        $sourceObjectReflection = $source->getObjectReflection();

        if ($sourceClassReflection
                ->hasProperty($targetPointReflection->getName()) ||
            !$sourceObjectReflection
                ->hasProperty($targetPointReflection->getName())
        ) {
            return null;
        }

        $sourcePointFqn = \sprintf(
            '%s::$%s',
            $sourceClassReflection->getName(),
            $targetPointReflection->getName()
        );

        $targetPointFqn = $this
            ->getPointFqnFromReflection($targetPointReflection);

        return $this->routeBuilder
            ->setDynamicSourcePoint($sourcePointFqn)
            ->setStaticTargetPoint($targetPointFqn)
            ->getRoute();
    }
}
