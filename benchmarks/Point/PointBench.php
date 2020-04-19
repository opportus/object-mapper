<?php

/**
 * This file is part of the opportus/object-mapper package.
 *
 * Copyright (c) 2018-2020 Clément Cazaud <clement.cazaud@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Opportus\ObjectMapper\Benchmarks\Point;

use Opportus\ObjectMapper\Benchmarks\BenchObject;
use Opportus\ObjectMapper\Point\MethodPoint;
use Opportus\ObjectMapper\Point\ParameterPoint;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * The point bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class PointBench
{
    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchConstruct()
    {
        new MethodPoint(\sprintf('%s.getA()', BenchObject::class));
        new ParameterPoint(\sprintf('%s.setB().$b', BenchObject::class));
        new MethodPoint(\sprintf('%s.getA()', BenchObject::class));
        new ParameterPoint(\sprintf('%s.setB().$b', BenchObject::class));
    }
}
