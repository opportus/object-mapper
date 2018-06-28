<?php

namespace Opportus\ObjectMapper\Map\Route\Point;

use Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidPropertyPointException;
use Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidPropertyPointSyntaxException;

/**
 * The property point.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PropertyPoint extends Point implements SourcePointInterface, TargetPointInterface
{
    /**
     * Constructs the property point.
     *
     * @param  string $fqn
     * @throws Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidPropertyPointException
     * @throws Opportus\ObjectMapper\Map\Route\Point\Exception\InvalidPropertyPointSyntaxException
     */
    public function __construct(string $fqn)
    {
        $regex = '/([A-Za-z\\\_]+)::\$([A-Za-z_]+)/';

        if (!preg_match($regex, $fqn, $matches)) {
            throw new InvalidPropertyPointSyntaxException(sprintf(
                '"%s" is not a property point as FQN of such is expected to have the following syntax: %s.',
                $fqn,
                $regex
            ));
        }

        list($matchedFqn, $matchedClassName, $matchedName) = $matches;

        try {
            $this->reflector = new \ReflectionProperty($matchedClassName, $matchedName);

        } catch (\ReflectionException $reflectionException) {
            throw new InvalidPropertyPointException(sprintf(
                '"%s" is not a property point. %s.',
                $fqn,
                $reflectionException->getMessage()
            ));
        }

        $this->fqn = $fqn;

        $this->reflector->setAccessible(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($object = null)
    {
        return $this->reflector->getValue($object);
    }
}

