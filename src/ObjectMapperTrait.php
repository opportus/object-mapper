<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\CheckPointSeizingException;
use Opportus\ObjectMapper\Map\MapInterface;

/**
 * The object mapper trait.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
trait ObjectMapperTrait
{
    /**
     * Maps source points values to target points following routes on the map.
     *
     * @param SourceInterface $source The source to map data from
     * @param TargetInterface $target The target to map data to
     * @param MapInterface    $map    An instance of map.
     * @return null|object            The instance of the operated target or
     *                                NULL if the there is no route mapping
     *                                source and target
     */
    private function mapObjects(
        SourceInterface $source,
        TargetInterface $target,
        MapInterface $map
    ): ?object {
        $routes = $map->getRoutes($source, $target);

        if (0 === \count($routes)) {
            return null;
        }

        foreach ($routes as $route) {
            $sourcePoint = $route->getSourcePoint();
            $targetPoint = $route->getTargetPoint();
            $checkPoints = $route->getCheckPoints();

            $checkPointSubject = $source->getPointValue($sourcePoint);

            try {
                foreach ($checkPoints as $checkPoint) {
                    $checkPointSubject = $checkPoint->control(
                        $checkPointSubject,
                        $route,
                        $map,
                        $source,
                        $target
                    );
                }
            } catch (CheckPointSeizingException $e) {
                continue;
            }

            $target->setPointValue($targetPoint, $checkPointSubject);
        }

        $target->operate();

        return $target->getInstance();
    }
}
