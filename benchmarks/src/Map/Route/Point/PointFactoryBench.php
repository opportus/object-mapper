<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2019 Clément Cazaud <clement.cazaud@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks\Src\Map\Route\Point;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Map\Route\Point\PointFactory;

/**
 * The point factory bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Src\Map\Route\Point
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointFactoryBench
{
    private $pointFactory;

    public function __construct()
    {
        $this->pointFactory = new PointFactory();
    }

    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchCreatePoint()
    {
        $this->pointFactory->createPoint(\sprintf('%s.getA()', BenchObject::class));
        $this->pointFactory->createPoint(\sprintf('%s.setB().$b', BenchObject::class));
        $this->pointFactory->createPoint(\sprintf('%s.getA()', BenchObject::class));
        $this->pointFactory->createPoint(\sprintf('%s.setB().$b', BenchObject::class));
    }
}
