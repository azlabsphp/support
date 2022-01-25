<?php

namespace Drewlabs\Support\DI\Traits;

use Closure;

trait BindginsResolversAware
{
    /**
     * 
     * @param string|null $name 
     * @return mixed 
     * @throws RuntimeException 
     */
    public function get(string $name = null)
    {
        if (null === $name) {
            return static::getInstance();
        }
        return $this->resolve($name);
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->bindings ?? []);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->bind((string)$offset, $value instanceof Closure ? $value : function () use ($value) {
            return $value;
        });
    }

    public function offsetUnset($offset): void
    {
        unset(
            $this->bindings[(string)$offset],
            $this->alias['abstracts'][$offset],
            $this->alias['concretes'][$offset]
        );
    }

    /**
     * Get the alias for an abstract if available.
     *
     * @param  string  $abstract
     * @return string
     */
    public function getAlias(?string $abstract = null)
    {
        if (null === $abstract) {
            return $this->aliases;
        }
        return !is_null($abstract_ = $this->aliases['concretes'][$abstract] ?? null)
            ? $this->getAlias($abstract_)
            : $abstract;
    }
}