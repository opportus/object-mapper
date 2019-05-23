<?php

namespace Opportus\ObjectMapper;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The class canonicalizer.
 *
 * @package Opportus\ObjectMapper
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ClassCanonicalizer implements ClassCanonicalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCanonicalFqcn($object): string
    {
        if (\is_object($object)) {
            $class = \get_class($object);

        } elseif (\is_string($object)) {
            $class = $object;

            if (!\class_exists($class)) {
                throw new InvalidArgumentException(\sprintf(
                    'Argument "object" passed to "%s" is invalid. Expecting an argument of type string to be a FQCN, class "%s" does not exist.',
                    __METHOD__,
                    $class
                ));
            }

        } else{
            throw new InvalidArgumentException(\sprintf(
                'Argument "object" passed to "%s" is invalid. Expecting an argument of type object or string, got an argument of type "%s".',
                __METHOD__,
                \gettype($object)
            ));
        }

        // Checks for Doctrine2 proxies...
        if (false !== \strpos($class, "Proxies\\__CG__\\")) {
            $class = \mb_substr($class, \strlen("Proxies\\__CG__\\"), \strlen($class));
        }

        return $class;
    }
}
