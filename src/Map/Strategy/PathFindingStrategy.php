<?php

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\ClassCanonicalizerInterface;
use Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;
use Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface;
use Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The default path finding strategy.
 *
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PathFindingStrategy implements PathFindingStrategyInterface
{
    /**
     * @var Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     */
    private $classCanonicalizer;

    /**
     * @var Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface $mapDefinitionBuilder
     */
    private $mapDefinitionBuilder;

    /**
     * Constructs the path finding strategy.
     *
     * @param Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     * @param Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface $mapDefinitionBuilder
     */
    public function __construct(ClassCanonicalizerInterface $classCanonicalizer, MapDefinitionBuilderInterface $mapDefinitionBuilder)
    {
        $this->classCanonicalizer = $classCanonicalizer;
        $this->mapDefinitionBuilder = $mapDefinitionBuilder;
    }

    /**
     * {@inheritdoc}
     *
     * This behavior consists of guessing which is the appropriate point of the source to connect to each point of the target.
     *
     * A TargetPoint can be:
     *
     * - A public property (PropertyPoint)
     * - A parameter of a public setter or a public constructor (ParameterPoint)
     *
     * The connectable SourcePoint can be:
     *
     * - A public property having for name the same as the target point (PropertyPoint)
     * - A public getter having for name 'get'.ucfirst($targetPointName) and requiring no argument (MethodPoint)
     */
    public function getRouteCollection(object $source, $target): RouteCollectionInterface
    {
        $sourceFqcn = $this->classCanonicalizer->getCanonicalFqcn($source);
        $targetFqcn = $this->classCanonicalizer->getCanonicalFqcn($target);
        $targetClassReflection = new \ReflectionClass($targetFqcn);
        $sourceClassReflection = new \ReflectionClass($sourceFqcn);

        $mapDefinitionBuilder = $this->mapDefinitionBuilder->prepareMapDefinition();
        $targetPoints = $this->getTargetPoints($targetClassReflection, \is_object($target));

        foreach ($targetPoints as $targetPoint) {
            $sourcePoint = $this->findSourcePoint($sourceClassReflection, $targetPoint);

            if (null !== $sourcePoint) {
                $mapDefinitionBuilder = $mapDefinitionBuilder->addRoute($sourcePoint, $targetPoint);
            }
        }

        return $mapDefinitionBuilder->buildMapDefinition()->getRouteCollection();
    }

    /**
     * Gets the target points.
     *
     * @param \ReflectionClass $targetClassReflection
     * @param bool $isTargetInstantiated
     * @return array
     */
    private function getTargetPoints(\ReflectionClass $targetClassReflection, bool $isTargetInstantiated): array
    {
        $targetPoints = [];
        foreach ($targetClassReflection->getMethods() as $targetMethodReflection) {
            if ($targetMethodReflection->isPublic()) {
                if (0 === \strpos($targetMethodReflection->getName(), 'set') || (false === $isTargetInstantiated && '__construct' === $targetMethodReflection->getName())) {
                    if ($targetMethodReflection->getNumberOfParameters() > 0) {
                        foreach ($targetMethodReflection->getParameters() as $targetParameterReflection) {
                            $targetPoints[] = new ParameterPoint(\sprintf(
                                '%s::%s()::$%s',
                                $targetClassReflection->getName(),
                                $targetMethodReflection->getName(),
                                $targetParameterReflection->getName()
                            ));
                        }
                    }
                }
            }
        }

        foreach ($targetClassReflection->getProperties() as $targetPropertyReflection) {
            if ($targetPropertyReflection->isPublic()) {
                $targetPoints[] = new PropertyPoint(\sprintf(
                    '%s::$%s',
                    $targetClassReflection->getName(),
                    $targetPropertyReflection->getName()
                ));
            }
        }

        return $targetPoints;
    }

    /**
     * Finds the source point to connect to the target point.
     *
     * @param \ReflectionClass $sourceClassReflection
     * @param  Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface $targetPoint
     * @return null|Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface $sourcePoint
     */
    private function findSourcePoint(\ReflectionClass $sourceClassReflection, TargetPointInterface $targetPoint): ?SourcePointInterface
    {

        foreach ($sourceClassReflection->getMethods() as $sourceMethodReflection) {
            if ($sourceMethodReflection->isPublic()) {
                if ($sourceMethodReflection->getName() === \sprintf('get%s', ucfirst($targetPoint->getName()))) {
                    if ($sourceMethodReflection->getNumberOfRequiredParameters() === 0) {
                        return new MethodPoint(\sprintf(
                            '%s::%s()',
                            $sourceClassReflection->getName(),
                            $sourceMethodReflection->getName()
                        ));
                    }
                }
            }
        }

        foreach ($sourceClassReflection->getProperties() as $sourcePropertyReflection) {
            if ($sourcePropertyReflection->isPublic()) {
                if ($sourcePropertyReflection->getName() === $targetPoint->getName()) {
                    return new PropertyPoint(\sprintf(
                        '%s::$%s',
                        $sourceClassReflection->getName(),
                        $sourcePropertyReflection->getName()
                    ));
                }
            }
        }

        return null;
    }
}
