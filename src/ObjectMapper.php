<?php

/**
 * This file is part of the opportus/object-mapper project.
 *
 * Copyright (c) 2018-2021 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Map\MapInterface;

/**
 * The object mapper.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapper implements ObjectMapperInterface
{
    use ObjectMapperTrait;

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
    public function map(
        object $source,
        $target,
        ?MapInterface $map = null
    ): ?object {
        if (!$source instanceof SourceInterface) {
            $source = new Source($source);
        }

        if (!$target instanceof TargetInterface) {
            try {
                $target = new Target($target);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(2, '', 0, $exception);
            }
        }

        $map = $map ?? $this->mapBuilder
            ->addStaticPathFinder()
            ->getMap();

        return $this->mapSourceToTarget($source, $target, $map);
    }
}
