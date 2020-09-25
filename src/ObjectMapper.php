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
use Opportus\ObjectMapper\Map\MapBuilderInterface;

/**
 * The object mapper.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class ObjectMapper implements ObjectMapperInterface
{
    /**
     * @var MapBuilderInterface $mapBuilder
     */
    private $mapBuilder;

    /**
     * Constructs the object mapper.
     *
     * @param MapBuilderInterface $mapBuilder
     */
    public function __construct(MapBuilderInterface $mapBuilder)
    {
        $this->mapBuilder = $mapBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function map(object $source, $target, ?MapInterface $map = null): ?object
    {
        $source = ($source instanceof SourceInterface) ? $source : new Source($source);
        $target = ($target instanceof TargetInterface) ? $target : new Target($target);
        $map    = $map ?? $this->mapBuilder
                ->addStaticPathFinder()
                ->getMap();

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

        return $target->getInstance();
    }
}
