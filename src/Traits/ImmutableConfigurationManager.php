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

namespace Drewlabs\Support\Traits;

/**
 * Class that uses this mixin should define a method for loading the and transforming the configurations into array.
 */
trait ImmutableConfigurationManager
{
    /**
     * Static class instance.
     *
     * @var self
     */
    private static $instance;

    /**
     * Configurations cache property.
     *
     * @var array
     */
    private $config;

    /**
     * Private constructor to prevent users from calling new on the current class.
     */
    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (null === static::$instance) {
            $self = new static();
            $self = drewlabs_core_create_attribute_setter('config', $self->config ?? [])($self);
            static::$instance = $self;
        }

        return static::$instance;
    }

    public function get($key = null, $default = null)
    {
        if (null === $key) {
            return array_merge($this->config, []);
        }
        $value = drewlabs_core_array_get($this->config, $key, $default);

        return null === $value ? ($default instanceof \Closure ? $default() : $default) : $value;
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return null !== drewlabs_core_array_get($offset, null);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset, null);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Configuration manager class is a readonly class, operations changing the class state are not allowed');
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Configuration manager class is a readonly class, operations changing the class state are not allowed');
    }
}
