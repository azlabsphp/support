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

namespace Drewlabs\Support\Traits;

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Reflector;

trait ImmutableConfig
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

    /**
     * Return the singleton instance.
     *
     * @return self
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = Reflector::propertySetter('config', [])(new self());
        }

        return self::$instance;
    }

    public static function configure(array $config = [])
    {
        $self = Reflector::propertySetter('config', $config ?? [])(new self());

        return self::$instance = $self;
    }

    public function get($key = null, $default = null)
    {
        if (null === $key) {
            return array_merge($this->config, []);
        }

        return null === ($value = Arr::get($this->config, $key, $default)) ? ($default instanceof \Closure ? $default() : $default) : $value;
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return null !== Arr::get($offset, null);
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
        throw new \RuntimeException('Configuration manager class is a readonly class, changing it state is not allowed');
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Configuration manager class is a readonly class, ]changing it state is not allowed');
    }
}
