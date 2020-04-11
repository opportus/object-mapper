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

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Opportus\ObjectMapper\Exception\InvalidOperationException;

/**
 * The abstract immutable collection.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
abstract class AbstractImmutableCollection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @var array $items
     */
    private $items = [];

    /**
     * Constructs the collection.
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Returns the collection as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return \count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidOperationException
     */
    public function offsetSet($offset, $value)
    {
        throw new InvalidOperationException(\sprintf(
            'Operation "%s" is invalid: attempting to set an element of an immutable array.',
            __METHOD__
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidOperationException
     */
    public function offsetUnset($offset)
    {
        throw new InvalidOperationException(\sprintf(
            'Operation "%s" is invalid: attempting to unset an element of an immutable array.',
            __METHOD__
        ));
    }
}
