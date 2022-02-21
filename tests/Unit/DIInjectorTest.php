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

use Drewlabs\Support\DI\Injector;
use Drewlabs\Support\Tests\Stubs\EventPublisher;
use Drewlabs\Support\Tests\Stubs\Logger;
use Drewlabs\Support\Tests\Stubs\LogProvider;
use Drewlabs\Support\Tests\Stubs\Publisher;
use Drewlabs\Support\Tests\TestCase;

class DIInjectorTest extends TestCase
{
    public function test_Injector_GetInstance_Returns_An_Instance_Of_Injector()
    {
        $injector = Injector::getInstance();
        $this->assertInstanceOf(Injector::class, $injector);
    }

    public function test_Injector_Returns_Same_Object_For_Successive_Get_Instance_Call()
    {
        $injector = Injector::getInstance();
        $this->assertSame($injector, Injector::getInstance());
    }

    public function test_Injector_Bind_Add_Definition_To_Class_Bindings()
    {
        $injector = Injector::getInstance();

        $injector->bind(Logger::class);

        $this->assertInstanceOf(Logger::class, Injector::getInstance()->get(Logger::class));

        $this->assertInstanceOf(LogProvider::class, Injector::getInstance()->get(Logger::class)->getProvider());
    }

    public function test_Injector_Alias_Creates_A_Binding_To_An_Implementation()
    {
        $injector = Injector::getInstance();

        $injector->bind(Logger::class);

        $injector->alias(Logger::class, 'log');

        $this->assertInstanceOf(Logger::class, Injector::getInstance()->get('log'));
    }

    public function test_Injector_Create_Singleton()
    {
        $injector = Injector::getInstance();

        $injector->singleton(Publisher::class, static function ($injector, $parameters = []) {
            return new EventPublisher(...$parameters);
        }, [false]);

        $this->assertFalse(Injector::getInstance()->get(Publisher::class)->getState());

        Injector::getInstance()->get(Publisher::class)->setState(true);

        $this->assertTrue(Injector::getInstance()->get(Publisher::class)->getState());
    }
}
