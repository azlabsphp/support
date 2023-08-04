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

use Drewlabs\Support\Tests\TestCase;
use Drewlabs\Support\Types\Nullable;

class NullableTest extends TestCase
{
    public function test_for_php_null()
    {
        $value = new Nullable(null);
        $this->assertNull($value->value());
        $this->assertFalse($value->hasValue());
        $this->assertTrue($value->isNull());
    }

    public function test_nullable_with_default()
    {
        $value = new Nullable(null, 3);
        $this->assertSame(3, $value->value());
        $this->assertTrue($value->hasValue());
        $this->assertFalse($value->isNull());
    }

    public function test_default_closure()
    {
        $value = new Nullable(null, static function () {
            return 'Hello World!';
        });
        $this->assertSame($value->value(), 'Hello World!');
    }

    public function test_php_primitive()
    {
        $value = new Nullable('Hello World!');
        $this->assertSame($value->value(), 'Hello World!');
        $this->assertTrue($value->hasValue());
        $this->assertFalse($value->isNull());
    }

    public function test_php_object()
    {
        $value = new Nullable(new \stdClass());
        $this->assertInstanceOf(\stdClass::class, $value->value());
        $this->assertTrue($value->hasValue());
        $this->assertFalse($value->isNull());
    }
}
