<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2022 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\CheckPointSeizingException;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
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
     * Transfers data of the source to the target following routes on the map.
     *
     * @param SourceInterface            $source The source to map data from
     * @param TargetInterface            $target The target to map data to
     * @param MapInterface               $map    An instance of map
     * @return null|object                       The instantiated and/or updated
     *                                           target or NULL if the there is
     *                                           no route mapping source and
     *                                           target
     * @throws InvalidOperationException         If the operation fails for any
     *                                           reason
     */
    private function mapSourceToTarget(
        SourceInterface $source,
        TargetInterface $target,
        MapInterface $map
    ): ?object {
        try {
            $routes = $map->getRoutes($source, $target);
        } catch (InvalidOperationException $exception) {
            throw new InvalidOperationException('', 0, $exception);
        }

        if (0 === \count($routes)) {
            return null;
        }

        foreach ($routes as $route) {
            $sourcePoint = $route->getSourcePoint();
            $targetPoint = $route->getTargetPoint();
            $checkPoints = $route->getCheckPoints();

            try {
                $checkPointSubject = $source->getPointValue($sourcePoint);
            } catch (InvalidArgumentException | InvalidOperationException $exception) {
                throw new InvalidOperationException('', 0, $exception);
            }

            foreach ($checkPoints as $checkPoint) {
                try {
                    $checkPointSubject = $checkPoint->control(
                        $checkPointSubject,
                        $route,
                        $map,
                        $source,
                        $target
                    );
                } catch (CheckPointSeizingException $exception) {
                    continue 2;
                } catch (InvalidOperationException $exception) {
                    throw new InvalidOperationException('', 0, $exception);
                }
            }

            try {
                $target->setPointValue($targetPoint, $checkPointSubject);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidOperationException('', 0, $exception);
            }
        }

        $target->operate();

        return $target->getInstance();
    }
}
