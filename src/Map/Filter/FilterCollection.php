<?php

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
     * @param Opportus\ObjectMapper\Map\Filter\FilterInterface[] $filters
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function __construct(array $filters = [])
    {
        $indexedFilters = [];
        
        foreach ($filters as $filter) {
            if (!\is_object($filter) || !$filter instanceof FilterInterface) {
                throw new InvalidArgumentException(\sprintf(
                    'Argument "filters" passed to "%s" is invalid. Expects the array to contain elements of type "%s". Got an element of type "%s".',
                    __METHOD__,
                    FilterInterface::class,
                    \is_object($filter) ? \get_class($filter) : \gettype($filter)
                ));
            }

            $indexedFilters[$filter->getRouteFqn()] = $filter;
        }

        parent::__construct($indexedFilters);
    }
}
