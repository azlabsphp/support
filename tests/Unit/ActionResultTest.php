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

use function Drewlabs\Support\Proxy\ActionResult;

use Drewlabs\Support\Tests\Stubs\ActionResultValueStub;
use Drewlabs\Support\Tests\Stubs\ConsoleLogger;

use Drewlabs\Support\Tests\TestCase;

class ActionResultTest extends TestCase
{
    public function testActionResultValueMethod()
    {
        $actionResult = ActionResult(new ActionResultValueStub());
        $this->assertInstanceOf(ActionResultValueStub::class, $actionResult->value(), 'Expect the value() method to returned the cached value');
    }

    public function testActionResultJsonSerializeMethod()
    {
        $actionResult = ActionResult(new ConsoleLogger());
        $this->assertInstanceOf(ConsoleLogger::class, $actionResult->jsonSerialize(), 'Expect the jsonSerialize() method to returned the cached value');
    }

    public function testActionResultToArrayMethod()
    {
        $actionResult = ActionResult(new ConsoleLogger());
        $this->assertIsArray($actionResult->toArray(), 'Expect the toArray() method to returned an array of value map to the cached value');
    }

    public function testActionResultDynamicMethod()
    {
        $actionResult = ActionResult(new ActionResultValueStub());
        $this->assertTrue($actionResult->exists());
    }
}
