<?php

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\Map\Route\Point\PointFactoryInterface;

/**
 * The route builder.
 *
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class RouteBuilder implements RouteBuilderInterface
{
    /**
     * @var Opportus\ObjectMapper\Map\Route\Point\PointFactoryInterface $pointFactory
     */
    private $pointFactory;

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
    public function buildRoute(string $sourcePointFqn, string $targetPointFqn): Route
    {
        $sourcePoint = $this->pointFactory->createPoint($sourcePointFqn);
        $targetPoint = $this->pointFactory->createPoint($targetPointFqn);

        return new Route($sourcePoint, $targetPoint);
    }
}
