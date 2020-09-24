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
use Opportus\ObjectMapper\Point\MethodParameterStaticTargetPoint;
use Opportus\ObjectMapper\Point\MethodStaticSourcePoint;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * The object point bench.
 *
 * @package Opportus\ObjectMapper\Benchmarks\Point
 * @author  Clément Cazaud <clement.cazaud@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectPointBench
{
    /**
     * @Revs(1000)
     * @Iterations(10)
     */
    public function benchConstruct()
    {
        new MethodStaticSourcePoint(\sprintf('%s.getA()', BenchObject::class));
        new MethodParameterStaticTargetPoint(\sprintf('%s.setB().$b', BenchObject::class));
        new MethodStaticSourcePoint(\sprintf('%s.getA()', BenchObject::class));
        new MethodParameterStaticTargetPoint(\sprintf('%s.setB().$b', BenchObject::class));
    }
}
