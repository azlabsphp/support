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

/**
 * @deprecated v2.2.x Implementation in drewlabs/php-value is
 *             more up to date and feature richer than this implementation
 */
trait ValueObject
{
    use Accessible;

    /**
     * Attribute container.
     *
     * @var object
     */
    protected $___attributes;

    /**
     * Defines the properties that can not been set using the attr array.
     *
     * @var array
     */
    protected $___guarded = [];

    /**
     * List of properties to hide when jsonSerializing the current object.
     *
     * @var array
     */
    protected $___hidden = [];

    /**
     * Indicated whether the bindings should load guarded properties.
     *
     * @var bool
     */
    protected $___loadGuardedAttributes = false;

    /**
     * @var bool
     */
    private $___isAssociative = false;

    /**
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * Creates an instance of Drewlabs\Support\Immutable\ValueObject::class.
     *
     * @param array|object $attributes
     */
    public function __construct($attributes = [])
    {
        $this->initializeAttributes();
        if (\is_array($attributes)) {
            $this->setAttributes($attributes);
        } elseif (\is_object($attributes) || ($attributes instanceof \stdClass)) {
            $this->fromStdClass($attributes);
        }
    }

    /**
     * Makes class attributes accessible through -> syntax.
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_internalGetAttribute($name);
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
     */
    public function __toString()
    {
        return $this->___attributes->__toString();
    }

    protected function __internalSerialized()
    {
        $fillables = $this->loadAttributesBindings();
        if ($this->___isAssociative) {
            return iterator_to_array(
                (function () use ($fillables) {
                    foreach ($fillables as $key => $value) {
                        if (!\in_array($key, $this->___hidden, true)) {
                            yield $value => $this->callAttributeSerializer($key);
                        }
                    }
                })()
            );
        }

        return iterator_to_array(
            (function () use ($fillables) {
                foreach (array_values($fillables) as $key) {
                    if (!\in_array($key, $this->___hidden, true)) {
                        yield $key => $this->callAttributeSerializer($key);
                    }
                }
            })()
        );
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * Creates a copy of the current object changing the changing old attributes
     * values with newly proivided ones
     */
    public function copyWith(array $attributes, $setGuarded = false)
    {
        $attributes = array_merge(
            $this->__internalSerialized(),
            $attributes ?? []
        );

        return (clone $this)->initializeAttributes()
            ->setAttributes(
                $attributes,
                $setGuarded
            );
    }

    /**
     * {@inheritDoc}
     *
     * Create an instance of {ValueObject} from a standard PHP class
     */
    public function fromStdClass($object_)
    {
        $fillables = $this->loadAttributesBindings();
        if ($this->___isAssociative) {
            foreach ($fillables as $key => $value) {
                if (property_exists($object_, $value) && $this->isNotGuarded($value, true)) {
                    $this->setAttribute($key, $object_->{$value});
                }
            }
        } else {
            foreach ($fillables as $key) {
                if (property_exists($object_, $key) && $this->isNotGuarded($key, true)) {
                    $this->setAttribute($key, $object_->{$key});
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * JSON Serializable method definition. It convert
     * class attributes to a json object aka PHP array, string, object etc...
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->__internalSerialized();
    }

    /**
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     */
    public function attributesToArray()
    {
        return iterator_to_array((function () {
            foreach ($this->___attributes as $key => $value) {
                if (!\in_array($key, $this->___hidden, true)) {
                    yield $key => $value;
                }
            }
        })());
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
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @return bool
     */
    public function getLoadGuardedAttributes()
    {
        return $this->___loadGuardedAttributes;
    }

    // region Array access method definitions

    /**
     * Query for the provided $key in the object attribute.
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @param \Closure|mixed|null $default
     *
     * @return mixed
     */
    public function getAttribute(string $key, $default = null)
    {
        $callback = function ($name, $default_) {
            $result = drewlabs_core_array_get(
                $this->___attributes ? $this->___attributes->toArray() : [],
                $name,
                function () use ($name) {
                    return $this->__get($name);
                }
            );

            return $result ?? (\is_callable($default_) ? (new \ReflectionFunction($default_))->invoke() : $default_);
        };

        return $this->_propertyGetterExists($key) || $this->_propertySerializerExists($key) ?
            $this->callAttributeSerializer($key) ?? (\is_callable($default) ?
                (new \ReflectionFunction($default))->invoke() :
                $default) : $callback($key, $default);
    }

    /**
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @return self
     */
    public function setHidden(array $value)
    {
        $this->___hidden = $value;

        return $this;
    }

    /**
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->___hidden;
    }

    /**
     * Merge hidden property values.
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @return self
     */
    public function mergeHidden(?array $value = [])
    {
        $this->___hidden = array_merge($this->___hidden ?: [], $value ?: []);

        return $this;
    }

    /**
     * Merge object attributes.
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @return self
     */
    public function merge(array $attributes)
    {
        return $this->copyWith($attributes);
    }

    /**
     * Copy the current object.
     *
     * @return self
     */
    public function copy()
    {
        return $this->copyWith([]);
    }

    /**
     * @return array
     */
    final public function getRawAttributes()
    {
        return $this->___attributes->toArray();
    }

    /**
     * Indicates wheter json attribute definition is an
     * associative array or not along the list of property mappings.
     *
     * @return array
     */
    protected function loadAttributesBindings()
    {
        return $this->getJsonableAttributes();
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function callAttributeDeserializer($name, $value)
    {
        if ($this->_propertySetterExists($name)) {
            return $this->{'set'.drewlabs_core_strings_as_camel_case($name).'Attribute'}($value);
        }
        if ($this->_propertyDeserializerExists($name)) {
            return $this->{'deserialize'.drewlabs_core_strings_as_camel_case($name).'Attribute'}($value);
        }

        return $value;
    }

    protected function callAttributeSerializer($name)
    {
        if ($this->_propertyGetterExists($name)) {
            return $this->{'get'.drewlabs_core_strings_as_camel_case($name).'Attribute'}();
        }
        if ($this->_propertySerializerExists($name)) {
            return $this->{'serialize'.drewlabs_core_strings_as_camel_case($name).'Attribute'}();
        }

        return $this->___attributes[$name] ?? null;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isNotGuarded($value, bool $load = false)
    {
        return $load ? true : !\in_array($value, $this->___guarded, true);
    }

    /**
     * @return self
     */
    protected function initializeAttributes()
    {
        $this->___attributes = new PhpStdClass();
        $this->___isAssociative = drewlabs_core_array_is_full_assoc(
            $this->getJsonableAttributes()
        );

        return $this;
    }

    /**
     * @return mixed
     */
    final protected function getRawAttribute(string $name)
    {
        return $this->___attributes[$name] ?? null;
    }

    /**
     * @param mixed $value
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @return self
     */
    final protected function setRawAttribute(string $name, $value)
    {
        $this->___attributes[$name] = $value;

        return $this;
    }

    /**
     * Attributes setter internal method.
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @param bool $setGuarded
     *
     * @return $this
     */
    protected function setAttributes(array $attributes, $setGuarded = false)
    {
        $this->___loadGuardedAttributes = $setGuarded;
        $fillables = $this->loadAttributesBindings();
        if ($this->___isAssociative) {
            foreach ($fillables as $key => $value) {
                if (\array_key_exists($value, $attributes) && $this->isNotGuarded($value, $setGuarded)) {
                    $this->setAttribute($key, $attributes[$value]);
                }
            }
        } else {
            foreach ($fillables as $key) {
                if (\array_key_exists($key, $attributes) && $this->isNotGuarded($key, $setGuarded)) {
                    $this->setAttribute($key, $attributes[$key]);
                }
            }
        }

        return $this;
    }

    // endregion magic methods

    /**
     * Internal attribute setter method.
     *
     * @param mixed $value
     * @param bool is_assoc
     * @param array<string> is_assoc
     *
     * @return self
     */

    /**
     * Internal attribute setter method.
     *
     * @deprecated v2.2.x Implementation in drewlabs/php-value is
     *             more up to date and feature richer than this implementation
     *
     * @param mixed $value
     *
     * @return $this
     */
    private function setAttribute(string $name, $value)
    {
        $result = $this->callAttributeDeserializer($name, $value);
        if (null !== $result) {
            $this->___attributes[$name] = $result;
        }

        return $this;
    }

    /**
     * Internal Attribute getter method.
     *
     * @return mixed
     */
    private function _internalGetAttribute(string $name)
    {
        $fillables = $this->loadAttributesBindings() ?? [];
        if (!$this->___isAssociative) {
            return $this->callAttributeSerializer($name);
        }
        if (null !== $this->___attributes[$name] ?? null) {
            return $this->callAttributeSerializer($name);
        }
        if (\array_key_exists($name, $fillables)) {
            return $this->callAttributeSerializer($name);
        }
        if ($key = drewlabs_core_array_search($name, $fillables)) {
            return $this->callAttributeSerializer($key);
        }

        return null;
    }

    private function _propertyGetterExists($name)
    {
        return method_exists($this, 'get'.drewlabs_core_strings_as_camel_case($name).'Attribute');
    }

    private function _propertySerializerExists($name)
    {
        return method_exists($this, 'serialize'.drewlabs_core_strings_as_camel_case($name).'Attribute');
    }

    private function _propertySetterExists($name)
    {
        return method_exists($this, 'set'.drewlabs_core_strings_as_camel_case($name).'Attribute');
    }

    private function _propertyDeserializerExists($name)
    {
        return method_exists($this, 'deserialize'.drewlabs_core_strings_as_camel_case($name).'Attribute');
    }
}
