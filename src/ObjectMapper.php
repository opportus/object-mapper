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
            try {
                $source = new Source($source);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(
                    1,
                    __METHOD__,
                    '',
                    0,
                    $exception
                );
            }
        }

        if (!$target instanceof TargetInterface) {
            try {
                $target = new Target($target);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(
                    2,
                    __METHOD__,
                    '',
                    0,
                    $exception
                );
            }
        }

        $map = $map ?? $this->mapBuilder
            ->addStaticPathFinder()
            ->getMap();

        try {
            return $this->mapSourceToTarget($source, $target, $map);
        } catch (InvalidOperationException $exception) {
            throw new InvalidOperationException(__METHOD__, '', 0, $exception);
        }
    }
}
