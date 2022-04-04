<?php

/*
 * This file is part of the ULID package.
 *
 * (c) Guilherme Felix da Silva Maciel <12631274+guifelix@users.noreply.github.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Guifelix\Ulid\Benchmark;

use Guifelix\Ulid\Ulid;

class UlidBench
{
    /**
     * @Revs(10000)
     */
    public function benchGenerate()
    {
        Ulid::generate();
    }
}
