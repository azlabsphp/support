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

use Drewlabs\Contracts\Support\Proxy\ObjectMethodProxyInterface;
use Drewlabs\Support\Traits\ObjectMethodsProxy;

/**
 * @method void log()
 */
class ProxyTestClass implements ObjectMethodProxyInterface
{
    use ObjectMethodsProxy;
}
