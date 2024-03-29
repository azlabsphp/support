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

namespace Drewlabs\Support\Tests\Unit;

use Drewlabs\Contracts\Support\Actions\ActionPayload;

use function Drewlabs\Support\Proxy\Action;

use Drewlabs\Support\Tests\TestCase;

class ActionTest extends TestCase
{
    public function test_create_action_instance_from_array_parameter()
    {
        $action = Action(['type' => 'SELECT', 'payload' => 1]);
        $this->assertSame('SELECT', $action->type());
        $this->assertInstanceOf(ActionPayload::class, $action->payload());
    }

    public function test_create_action_instance_from_variadic_param()
    {
        $action = Action('SELECT', ['where' => ['id', 3]]);
        $this->assertSame('SELECT', $action->type());
        $this->assertInstanceOf(ActionPayload::class, $action->payload());
        $this->assertSame([['where' => ['id', 3]]], $action->payload()->toArray());
    }
}
