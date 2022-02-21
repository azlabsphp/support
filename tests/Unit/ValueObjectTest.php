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

use Drewlabs\Support\Compact\PhpStdClass;
use Drewlabs\Support\Immutable\Exceptions\ImmutableObjectException;
use Drewlabs\Support\Tests\Stubs\FileLogger;
use Drewlabs\Support\Tests\Stubs\Message;
use Drewlabs\Support\Tests\Stubs\ValueObjectStub;
use Drewlabs\Support\Tests\TestCase;

class ValueObjectTest extends TestCase
{
    public function testValueObjectCopyWithMethod()
    {
        $message = new Message(
            [
                'From' => 'xxx-xxx-xxx',
                'To' => 'yyy-yyy-yyy',
                'Logger' => new FileLogger(),
            ]
        );
        $message_z = $message->copyWith([
            'From' => 'zzz-zzz-zzz',
        ]);
        $message_z->Logger->updateMutable();
        $this->assertTrue('xxx-xxx-xxx' === $message->from);
        $this->assertNotSame($message_z->from, $message->from);
    }

    public function testValueObjectImmutableSetterMethod()
    {
        $message = new Message([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->expectException(ImmutableObjectException::class);
        $message->From = 'zzz-zzz-yyy';

        $this->assertTrue(true);
    }

    public function testValueObjectImmutableUnsetMethodThrowsException()
    {
        $message = new Message([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->expectException(ImmutableObjectException::class);
        unset($message->From);
    }

    public function testValueObjectImmutableOffsetSetMethodThrowsException()
    {
        $message = new Message([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->assertTrue($message->offsetExists('from'));
        $this->assertSame($message->offsetGet('from'), 'xxx-xxx-xxx', 'Expect from property to equals xxx-xxx-xxx');
        $this->expectException(ImmutableObjectException::class);
        $message->offsetSet('From', 'YYY-YYY-YY');
    }

    public function testJsonSerializeMethod()
    {
        $message = new Message([
            'From' => 'xxx-xxx-xxx',
            'To' => 'yyy-yyy-yyy',
            'Logger' => new FileLogger(),
        ]);
        $this->assertTrue(drewlabs_core_strings_contains($message->jsonSerialize()['From'], 'xxx'), 'Expect the from property to contains xxx');
    }

    public function testNonAssocValueObject()
    {
        $value = new ValueObjectStub([
            'name' => 'Azandrew Sidoine',
            'address' => 'KEGUE, LOME - TOGO',
        ]);
        $this->assertTrue(drewlabs_core_strings_contains($value->name, 'Azandrew'), 'Expect the value name property to be a string that contains Azandrew');
    }

    public function testPropertiesGetterMethod()
    {
        $value = new ValueObjectStub([
            'name' => 'Azandrew Sidoine',
            'address' => 'KEGUE, LOME - TOGO',
        ]);

        $this->assertSame($value->name, 'Azandrew Sidoine', 'Expect name property value to equals Azandrew Sidoine');
    }

    public function testFromStdClassMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';
        $object->Logger = new FileLogger();
        $message = (new Message())->fromStdClass($object);
        $this->assertSame($message->From, 'xxx-xxx-xxx', 'Expect from property value to equals xxx-xxx-xxx');
    }

    public function testAttributesToArrayMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';
        $object->Logger = new FileLogger();
        $message = (new Message())->fromStdClass($object);
        $this->assertIsArray($message->attributesToArray(), 'Expect attributesToArray() method to return an array');
        $this->assertSame($message['from'], 'xxx-xxx-xxx');
    }

    public function testToStringMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';
        $object->Logger = new FileLogger();

        $address = new \stdClass();
        $address->email = 'test@example.com';
        $geolocation = new \stdClass();
        $geolocation->lat = '6.09834355';
        $geolocation->long = '4.8947352';
        $address->geolocation = $geolocation;
        $object->Address = $address;
        $message = (new Message())->fromStdClass($object);
        $this->assertIsString((string) $message, 'Expect object to be stringeable');
    }

    public function testValueObjectGetIteratorMethod()
    {
        $object = new \stdClass();
        $object->From = 'xxx-xxx-xxx';
        $object->To = 'yyy-yyy-yyy';

        $address = new \stdClass();
        $address->email = 'test@example.com';
        $geolocation = new PhpStdClass();
        $geolocation->lat = '6.09834355';
        $geolocation->long = '4.8947352';
        $address->geolocation = $geolocation;
        $object->Address = $address;
        $message = (new Message())->fromStdClass($object);
        $this->assertSame('test@example.com', $message->getAttribute('address.email'), 'Expect email to equals test@example.com');
    }
}
