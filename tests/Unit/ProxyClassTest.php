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

namespace Drewlabs\Support\Tests\Unit;

use Drewlabs\Support\Tests\Stubs\ConsoleLogger;
use Drewlabs\Support\Tests\Stubs\ProxyTestClass;
use Drewlabs\Support\Tests\TestCase;

class ProxyClassTest extends TestCase
{
    public function testForwardCallsToLogMethod()
    {
        $result = (new ProxyTestClass())->proxy(new ConsoleLogger(), 'log', [], static function () {
            return 'Default log message...';
        });
        $this->assertSame('Logging to the console...', $result, 'Expect the proxy object to forward the call successfully');
    }

    public function testForwardCallsToDefaultCallback()
    {
        $result = (new ProxyTestClass())->proxy(new ConsoleLogger(), 'notExistMethod', [], static function () {
            return 'Default log message...';
        });
        $this->assertSame('Default log message...', $result, 'Expect the proxy object to forward the call successfully');
    }
}
