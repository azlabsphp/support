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
use Drewlabs\Support\Tests\Stubs\MethodOverloadClass;
use Drewlabs\Support\Tests\Stubs\TestClass;
use Drewlabs\Support\Tests\TestCase;

class MethodOverloadingTest extends TestCase
{
    public function testOverloadedLogFunction()
    {
        // print_r((new TestClass)->log(new ConsoleLogger(), 'Hello'));
        $this->assertSame('Logging to the console...', (new TestClass())->log(new ConsoleLogger(), 'Hello World'), 'Expect ConsoleLogger::log to be called');
        $this->assertTrue('METHOD B' === (new MethodOverloadClass())->someMethod(new \DateTime(), [], 20), 'Expect the return value of MethodOverloadClass::someMethod to return METHOD B');
        $this->assertTrue('METHOD C' === (new MethodOverloadClass())->someMethod([], []), 'Expect the return value of MethodOverloadClass::someMethod to return METHOD C');
    }
}
