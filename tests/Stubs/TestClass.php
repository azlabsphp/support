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

namespace Drewlabs\Support\Tests\Stubs;

use Drewlabs\Support\Traits\Overloadable;

class TestClass
{
    use Overloadable;

    public function log(...$args)
    {
        return $this->overload($args, [
            static function (ConsoleLogger $logger, ?array $optional2 = null, $optional = null) {
                return $logger->log();
            },
            static function (ConsoleLogger $logger, ?string $optional = null, $optional2 = null) {
                return $logger->log();
            },
        ]);
    }
}
