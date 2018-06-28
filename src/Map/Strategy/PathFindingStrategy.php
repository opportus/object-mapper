<?php

namespace Opportus\ObjectMapper\Map\Strategy;

use Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollectionInterface;
use Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface;
use Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Map\Route\Point\MethodPoint;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;;

/**
 * The default path finding strategy.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Strategy
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PathFindingStrategy implements PathFindingStrategyInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface $mapDefinitionBuilder
     */
    protected $mapDefinitionBuilder;

    /**
     * Constructs the path finding strategy.
     *
     * @var Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface $mapDefinitionBuilder
     */
    public function __construct(MapDefinitionBuilderInterface $mapDefinitionBuilder)
    {
        $this->mapDefinitionBuilder = $mapDefinitionBuilder;
    }

    /**
     * {@inheritdoc}
     *
     * This behavior consists of guessing what is the appropriate point of the source class to connect to each point of the target class.
     *
     * A TargetPoint can be:
     *
     * - A public property (PropertyPoint)
     * - A parameter of a public setter or a public constructor (ParameterPoint)
     *
     * The corresponding SourcePoint can be:
     *
     * - A public property having for name the same as the target point (PropertyPoint)
     * - A public getter having for name 'get'.ucfirst($targetPointName) and requiring no argument (MethodPoint)
     */
    public function getRouteCollection(string $sourceClassFqn, string $targetClassFqn) : RouteCollectionInterface
    {
        if (!class_exists($sourceClassFqn)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 1 passed to %s is invalid. Class %s does not exist.',
                __METHOD__,
                $sourceClassFqn
            ));
        }

        if (!class_exists($targetClassFqn)) {
            throw new InvalidArgumentException(sprintf(
                'Argument 2 passed to %s is invalid. Class %s does not exist.',
                __METHOD__,
                $targetClassFqn
            ));
        }

        $targetPoints         = $this->guessTargetPoints($targetClassFqn);
        $mapDefinitionBuilder = $this->mapDefinitionBuilder->prepareMapDefinition();

        foreach ($targetPoints as $targetPoint) {
            $sourcePoint = $this->guessSourcePoint($sourceClassFqn, $targetPoint);

            if (null !== $sourcePoint) {
                $mapDefinitionBuilder = $mapDefinitionBuilder->addRoute($sourcePoint, $targetPoint);
            }
        }

        return $mapDefinitionBuilder->buildMapDefinition()->getRouteCollection();
    }

    /**
     * Guesses the target points.
     *
     * @param  string $targetClassFqn
     * @return array
     */
    private function guessTargetPoints(string $targetClassFqn) : array
    {
        $targetPoints          = array();
        $targetClassReflection = new \ReflectionClass($targetClassFqn);

        foreach ($targetClassReflection->getMethods() as $targetClassMethodReflection) {
            if ($targetClassMethodReflection->isPublic()) {
                if (preg_match('/set[A-Z][a-zA-Z]*/', $targetClassMethodReflection->getName()) ||
                    $targetClassMethodReflection->getName() === '__construct' ||
                    $targetClassMethodReflection->getName() === 'update'
                ) {
                    if ($targetClassMethodReflection->getNumberOfParameters() > 0) {
                        foreach ($targetClassMethodReflection->getParameters() as $targetClassMethodParameterReflection) {
                            $targetPoints[] = new ParameterPoint(sprintf(
                                '%s::%s()::$%s',
                                $targetClassReflection->getName(),
                                $targetClassMethodReflection->getName(),
                                $targetClassMethodParameterReflection->getName()
                            ));
                        }
                    }
                }
            }
        }

        foreach ($targetClassReflection->getProperties() as $targetClassPropertyReflection) {
            if ($targetClassPropertyReflection->isPublic()) {
                $targetPoints[] = new PropertyPoint(sprintf(
                    '%s::$%s',
                    $targetClassReflection->getName(),
                    $targetClassPropertyReflection->getName()
                ));
            }
        }

        return $targetPoints;
    }

    /**
     * Guesses the source point to connect to the passed target point.
     *
     * @param  string $sourceClassFqn
     * @param  Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface $targetPoint
     * @return null|Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface $sourcePoint
     */
    private function guessSourcePoint(string $sourceClassFqn, TargetPointInterface $targetPoint) : ?SourcePointInterface
    {
        $sourceClassReflection = new \ReflectionClass($sourceClassFqn);

        foreach ($sourceClassReflection->getMethods() as $sourceClassMethodReflection) {
            if ($sourceClassMethodReflection->isPublic()) {
                if ($sourceClassMethodReflection->getName() === 'get'.ucfirst($targetPoint->getName())) {
                    if ($sourceClassMethodReflection->getNumberOfRequiredParameters() === 0) {
                        return new MethodPoint(sprintf(
                            '%s::%s()',
                            $sourceClassReflection->getName(),
                            $sourceClassMethodReflection->getName()
                        ));
                    }
                }
            }
        }

        foreach ($sourceClassReflection->getProperties() as $sourceClassPropertyReflection) {
            if ($sourceClassPropertyReflection->isPublic()) {
                if ($sourceClassPropertyReflection->getName() === $targetPoint->getName()) {
                    return new PropertyPoint(sprintf(
                        '%s::$%s',
                        $sourceClassReflection->getName(),
                        $sourceClassPropertyReflection->getName()
                    ));
                }
            }
        }

        return null;
    }
}

