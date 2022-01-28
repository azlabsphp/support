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

namespace Drewlabs\Support\DI;

interface ContextualBindingsBuilder
{
    /**
     * @param string $abstract
     *
     * @return self
     */
    public function require($abstract);

    /**
     * @param string|\Closure|array $implementation
     *
     * @return void
     */
    public function provide($implementation);
}
