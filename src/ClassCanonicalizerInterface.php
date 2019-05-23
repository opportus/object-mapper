<?php

namespace Opportus\ObjectMapper;

/**
 * The class canonicalizer interface.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface ClassCanonicalizerInterface
{
    /**
     * Gets the canonical FQCN.
     *
     * @param  string|object $object
     * @return string
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function getCanonicalFqcn($object): string;
}
