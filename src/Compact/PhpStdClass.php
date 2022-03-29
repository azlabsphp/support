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

namespace Drewlabs\Support\Compact;

/**
 * @deprecated v2.2.x Prefer use of PHP {@see ArrayObject} class or the 
 *             {@see Accessible} class from drewlabs/php-value package
 * 
 * @package Drewlabs\Support\Compact
 */
class PhpStdClass extends \stdClass implements \ArrayAccess
{
    /**
     * {@inheritDoc}
     */
    public function __clone()
    {
        foreach ($this as $key => $value) {
            if (\is_object($value)) {
                $this[$key] = clone $value;
            }
        }

        return $this;
    }

    public function __isset(string $name)
    {
        return property_exists($this, $name) && (null !== $this[$name]);
    }

    // TODO : review the object formatting
    public function __toString()
    {
        return json_encode($this, \JSON_PRETTY_PRINT);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        $this->validatePropertyName($offset);

        return property_exists($this, $offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $this->validatePropertyName($offset);

        return $this->offsetExists($offset) ? $this->{$offset} : null;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->validatePropertyName($offset);
        $this->{$offset} = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->validatePropertyName($offset);
        unset($this->{$offset});
    }

    /**
     * Object oriented implementation of the property exists function on this object.
     */
    public function propertyExists($prop): bool
    {
        return property_exists($this, $prop);
    }

    public function isEmpty()
    {
        if (empty(get_object_vars($this))) {
            return true;
        }
        // Iterate over object properties and return false if one property is set
        foreach ($this as $v) {
            if (isset($v)) {
                return false;
            }
        }
        // Return true if all properties of the object are not set
        return true;
    }

    /**
     * Provides an enumerator on the object properties key and value.
     * It provides an object oriented way of looping through object
     * keys and values.
     *
     * Can be used instead of:
     * ```php
     * $p = new stdClass;
     * foreach ($p as $key => $value ) {
     *  //... Provides implementation details
     * }
     * ```
     *
     * @return \Generator<int, mixed, mixed, void>
     */
    public function each(\Closure $callback)
    {
        foreach ($this as $key => $value) {
            yield $callback(...[$key, $value]);
        }
    }

    /**
     * Provides an enumerator on the object properties key and value.
     *
     * Can be used instead of:
     * ```php
     * $p = new stdClass;
     * foreach ($p as $key => $value ) {
     *  //... Provides implementation details
     * }
     * ```
     *
     * @return \Generator<int, mixed, mixed, void>
     */
    public function forEach(\Closure $callback)
    {
        return $this->forEach($callback);
    }

    public function toArray(): array
    {
        return iterator_to_array(
            (function () {
                foreach ($this as $key => $value) {
                    if (\is_object($value) && method_exists($value, 'toArray')) {
                        yield $key => $value->toArray();
                        continue;
                    }
                    if (\is_object($value)) {
                        yield $key => (array) $value;
                        continue;
                    }
                    yield $key => $value;
                }
            })()
        );
    }

    private function validatePropertyName($property)
    {
        if (!\is_string($property)) {
            throw new \InvalidArgumentException('Object accessible property must be of type string');
        }
    }
}
