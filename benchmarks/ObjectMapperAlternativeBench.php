<?php

namespace Opportus\ObjectMapper\Benchmarks;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectMapperAlternativeBench
{
    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithNativeMapping()
    {
        $source = new BenchObject(1);
        $source->setB(11);

        $target = new BenchObject($source->getA());
        $target->setB($source->getB());
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchMapWithPropertyAccessMapping()
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $source = new BenchObject(1);
        $source->setB(11);

        $target = new BenchObject($propertyAccessor->getValue($source, 'a'));
        $propertyAccessor->setValue($target, 'b', $propertyAccessor->getValue($source, 'b'));
    }
}
