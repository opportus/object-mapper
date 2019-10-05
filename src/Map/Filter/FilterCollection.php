<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Map\Filter;

use Opportus\ObjectMapper\AbstractImmutableCollection;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The filter collection.
 *
 * @package Opportus\ObjectMapper\Map\Filter
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class FilterCollection extends AbstractImmutableCollection
{
    /**
     * Constructs the filter collection.
     *
     * @param FilterInterface[] $filters
     * @throws InvalidArgumentException
     */
    public function __construct(array $filters = [])
    {
        foreach ($filters as $filter) {
            if (!\is_object($filter) || !$filter instanceof FilterInterface) {
                throw new InvalidArgumentException(\sprintf(
                    'Argument "filters" passed to "%s" is invalid. Expects the array to contain elements of type "%s". Got an element of type "%s".',
                    __METHOD__,
                    FilterInterface::class,
                    \is_object($filter) ? \get_class($filter) : \gettype($filter)
                ));
            }
        }

        parent::__construct($filters);
    }
}
