<?php

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface;
use Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface;

/**
 * The route.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class Route implements RouteInterface
{
    /**
     * @var string $fqn
     */
    protected $fqn;

    /**
     * @var Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface $sourcePoint
     */
    protected $sourcePoint;

    /**
     * @var Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface $targetPoint
     */
    protected $targetPoint;

    /**
     * Constructs the route.
     *
     * @param Opportus\ObjectMapper\Map\Route\Point\SourcePointInterface $sourcePoint
     * @param Opportus\ObjectMapper\Map\Route\Point\TargetPointInterface $targetPoint
     */
    public function __construct(SourcePointInterface $sourcePoint, TargetPointInterface $targetPoint)
    {
        $this->fqn = sprintf('%s=>%s', $sourcePoint->getFqn(), $targetPoint->getFqn());

        $this->sourcePoint = $sourcePoint;
        $this->targetPoint = $targetPoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getFqn() : string
    {
        return $this->fqn;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourcePoint() : SourcePointInterface
    {
        return $this->sourcePoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetPoint() : TargetPointInterface
    {
        return $this->targetPoint;
    }

    /**
     * Deep clones the route.
     */
    public function __clone()
    {
        $this->sourcePoint = clone $this->sourcePoint;
        $this->targetPoint = clone $this->targetPoint;
    }
}

