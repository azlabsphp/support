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

namespace Drewlabs\Support\Immutable\Traits;

use Drewlabs\Support\Compact\PhpStdClass;
use Drewlabs\Support\Immutable\Exceptions\ImmutableObjectException;

trait ValueObject
{
    /**
     * Creates an instance of Drewlabs\Support\Immutable\ValueObject::class.
     *
     * @param array|object $attributes
     */
    public function __construct($attributes = [])
    {
        $this->___attributes = new PhpStdClass();
        if (\is_array($attributes)) {
            $this->setAttributes($attributes);
        } elseif (\is_object($attributes) || ($attributes instanceof \stdClass)) {
            $this->fromStdClass($attributes);
        } else {
            // Else if null is provided as parameter, build the object with null params
            $this->setAttributes([]);
        }
    }

    /**
     * Makes class attributes accessible through -> syntax.
     *
     * @param  $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        [$is_assoc, $properties] = $this->loadAttributesBindings();

        return $this->_internalGetAttribute($name, $is_assoc, $properties);
    }

    /**
     * Makes sure the object properties are not set by external code making the object immutable.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws ImmutableObjectException
     */
    public function __set($name, $value)
    {
        throw new ImmutableObjectException(__CLASS__);
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function __toString()
    {
        return $this->___attributes->__toString();
    }

    //endregion ArrayAccess method definitions

    //region magic methods
    public function __isset($name)
    {
        return isset($this->___attributes[$name]);
    }

    public function __unset($name)
    {
        throw new ImmutableObjectException(__CLASS__);
    }

    /**
     * {@inheritDoc}
     */
    public function __clone()
    {
        $this->___attributes = clone $this->___attributes;
    }

    private function __internalSerialized()
    {
        $attributes = [];
        [$is_assoc, $properties] = $this->loadAttributesBindings();
        if ($is_assoc) {
            foreach ($properties as $key => $value) {
                if (!\in_array($key, $this->___hidden, true)) {
                    $attributes[$value] = $this->_internalGetAttribute($key, $is_assoc, $properties);
                }
            }
        } else {
            foreach ($properties as $key) {
                if (!\in_array($key, $this->___hidden, true)) {
                    $attributes[$key] = $this->_internalGetAttribute($key, $is_assoc, $properties);
                }
            }
        }

        return $attributes;
    }

    /**
     * {@inheritDoc}
     *
     * Creates a copy of the current object changing the changing old attributes
     * values with newly proivided ones
     */
    public function copyWith(array $attr, $set_guarded = false)
    {
        $attributes = array_merge($this->__internalSerialized(), $attr);

        return clone (new static())->setAttributes($attributes, $set_guarded);
    }

    /**
     * {@inheritDoc}
     *
     * Create an instance of {ValueObject} from a standard PHP class
     */
    public function fromStdClass($object_)
    {
        [$is_assoc, $properties] = $this->loadAttributesBindings();
        if ($is_assoc) {
            foreach ($properties as $key => $value) {
                if (property_exists($object_, $value) && $this->isNotGuarded($value, true)) {
                    $this->setAttribute($key, $object_->{$value}, $is_assoc, $properties);
                }
            }
        } else {
            foreach ($properties as $key) {
                if (property_exists($object_, $key) && $this->isNotGuarded($key, true)) {
                    $this->setAttribute($key, $object_->{$key}, $is_assoc, $properties);
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * JSON Serializable method definition. It convert
     * class attributes to a json object aka PHP array, string, object etc...
     */
    public function jsonSerialize()
    {
        return $this->__internalSerialized();
    }

    /**
     * {@inheritDoc}
     */
    public function attributesToArray()
    {
        $attributes = [];
        foreach ($this->___attributes as $key => $value) {
            // code...
            if (!\in_array($key, $this->___hidden, true)) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->jsonSerialize();
    }

    /**
     * [[loadGuardedAttributes]] property getter.
     *
     * @return bool
     */
    public function getLoadGuardedAttributes()
    {
        return $this->___loadGuardedAttributes;
    }

    //region Array access method definitions

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->___attributes->offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (\is_int($offset)) {
            return;
        }

        return $this->__get($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ImmutableObjectException Use the {copyWith} method to create
     *                                  a new object from the properties of the current object while changing the
     *                                  needed properties
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new ImmutableObjectException(__CLASS__);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ImmutableObjectException Use the {copyWith} method to create
     *                                  a new object from the properties of the current object while changing the
     *                                  needed properties to null
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new ImmutableObjectException(__CLASS__);
    }

    /**
     * Query for the provided $key in the object attribute.
     *
     * @param \Closure|mixed|null $default
     *
     * @return mixed
     */
    public function getAttribute(string $key, $default = null)
    {
        $getFromAttributesFunc = function () use ($key, $default) {
            $result = drewlabs_core_array_get(
                $this->___attributes ? $this->___attributes->toArray() : [],
                $key,
                function () use ($key) {
                    [$isassoc, $properties] = $this->loadAttributesBindings();
                    $properties = $isassoc ? array_keys($properties) : $properties;
                    if (\in_array($key, $properties, true)) {
                        return $this->___attributes[$key] ?? null;
                    }
                }
            );

            return $result ?? ((null !== $default && \is_callable($default)) ? (new \ReflectionFunction($default))->invoke() : $default);
        };

        return method_exists($this, 'serialize'.drewlabs_core_strings_as_camel_case($key).'Attribute') ? $this->callAttributeSerializer($key) : $getFromAttributesFunc();
    }

    /**
     * {@inheritDoc}
     *
     * Attributes setter internal method
     */
    protected function setAttributes(array $attributes, $set_guarded = false)
    {
        $this->___loadGuardedAttributes = $set_guarded;
        [$is_assoc, $values] = $this->loadAttributesBindings();
        if ($is_assoc) {
            foreach ($values as $key => $value) {
                if (\array_key_exists($value, $attributes) && $this->isNotGuarded($value, $set_guarded)) {
                    $this->setAttribute($key, $attributes[$value], $is_assoc, $values);
                }
            }
        } else {
            foreach ($values as $key) {
                if (\array_key_exists($key, $attributes) && $this->isNotGuarded($key, $set_guarded)) {
                    $this->setAttribute($key, $attributes[$key], $is_assoc, $values);
                }
            }
        }

        return $this;
    }

    /**
     * Get a boolean value indicating wheter json attribute definition is an
     * associative array or not along the list of property mappings.
     *
     * @return array
     */
    protected function loadAttributesBindings()
    {
        $json_attributes = $this->getJsonableAttributes();
        $is_assoc = drewlabs_core_array_is_full_assoc($json_attributes);

        return [$is_assoc, $json_attributes];
    }

    protected function callAttributeDeserializer($name, $value)
    {
        if (method_exists($this, 'deserialize'.drewlabs_core_strings_as_camel_case($name).'Attribute')) {
            return $this->{'deserialize'.drewlabs_core_strings_as_camel_case($name).'Attribute'}($value);
        }

        return $value;
    }

    protected function callAttributeSerializer($name)
    {
        if (method_exists($this, 'serialize'.drewlabs_core_strings_as_camel_case($name).'Attribute')) {
            return $this->{'serialize'.drewlabs_core_strings_as_camel_case($name).'Attribute'}();
        }

        return $this->___attributes[$name];
    }

    protected function isNotGuarded($value, bool $load = false)
    {
        return $load ? true : !\in_array($value, $this->___guarded, true);
    }

    final protected function getRawAttributes()
    {
        return (array) $this->___attributes;
    }

    //endregion magic methods

    /**
     * Internal attribute setter method.
     *
     * @param mixed $value
     * @param bool is_assoc
     * @param array<string> is_assoc
     *
     * @return void
     */
    private function setAttribute(string $name, $value, $is_assoc, $properties)
    {
        if ($is_assoc) {
            $properties = array_keys($properties);
        }
        if (\in_array($name, $properties, true)) {
            $this->___attributes[$name] = $this->callAttributeDeserializer($name, $value);
        }

        return $this;
    }

    /**
     * Internal Attribute getter method.
     *
     * @return mixed
     */
    private function _internalGetAttribute(string $name, bool $is_assoc, array $properties)
    {
        if ($is_assoc) {
            $properties = array_keys($properties);
        }
        if (\in_array($name, $properties, true)) {
            return isset($this->___attributes[$name]) ? $this->callAttributeSerializer($name) : null;
        }

        return null;
    }
}
