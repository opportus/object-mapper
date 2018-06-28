<?php

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\Map\Route\Point\PointFactoryInterface;

/**
 * The route builder.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteBuilder implements RouteBuilderInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\Route\Point\PointFactoryInterface $pointFactory
     */
    protected $pointFactory;

    /**
     * Constructs the route builder.
     *
     * @param Opportus\ObjectMapper\Map\Route\Point\PointFactoryInterface $pointFactory
     */
    public function __construct(PointFactoryInterface $pointFactory)
    {
        $this->pointFactory = $pointFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildRoute(string $sourcePointFqn, string $targetPointFqn) : RouteInterface
    {
        $sourcePoint = $this->pointFactory->createSourcePoint($sourcePointFqn);
        $targetPoint = $this->pointFactory->createTargetPoint($targetPointFqn);

        return new Route($sourcePoint, $targetPoint);
    }
}

