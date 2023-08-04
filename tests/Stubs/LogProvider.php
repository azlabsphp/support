<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Support\Tests\Stubs;

class LogProvider
{
    public function __construct()
    {
    }

    public function write(string $message)
    {
        var_dump(sprintf('Writing to console... %s', $message));
    }
}
