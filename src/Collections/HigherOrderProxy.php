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

namespace Drewlabs\Support\Collections;

class HigherOrderProxy
{
    /**
     * The collection being operated on.
     *
     * @var mixed
     */
    protected $collection;

    /**
     * The method being proxied.
     *
     * @var string
     */
    protected $method;

    /**
     * Create a new proxy instance.
     *
     * @param mixed  $collection
     * @param string $method
     *
     * @return self
     */
    public function __construct($collection, $method)
    {
        $this->method = $method;
        $this->collection = $collection;
    }

    /**
     * Proxy accessing an attribute onto the collection items.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->collection->{$this->method}(static function ($value) use ($key) {
            return \is_array($value) ? $value[$key] : $value->{$key};
        });
    }

    /**
     * Proxy a method call onto the collection items.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->collection->{$this->method}(static function ($value) use ($method, $parameters) {
            return $value->{$method}(...$parameters);
        });
    }
}