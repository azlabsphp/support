<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Support\Collections\Contracts;

interface Collectable
{
    /**
     * Collect the output of the a given data structure.
     *
     * @param CollectorInterface|callable $collector
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function collect(callable $collector);
}
