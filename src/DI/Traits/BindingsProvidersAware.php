<?php

namespace Drewlabs\Support\DI\Traits;

use LogicException;

trait BindingsProvidersAware
{
    /**
     * Creates a class or insterface binding
     * 
     * @param string $abstract 
     * @param mixed $concrete 
     * @param array $parameters 
     * @return void 
     * @throws TypeBindingException 
     */
    public function bind(string $abstract, $concrete = null, array $parameters = [])
    {
        [$abstract, $concrete, $parameters] = $this->buildBindingsParameters($abstract, $concrete, $parameters);
        return $this->createBinding($abstract, $concrete, $parameters, false);
    }

    /**
     * Creates a singleton class binding
     * 
     * @param string $abstract 
     * @param mixed $concrete 
     * @param array $parameters 
     * @return void 
     * @throws TypeBindingException 
     */
    public function singleton(string $abstract, $concrete = null, array $parameters = [])
    {
        [$abstract, $concrete, $parameters] = $this->buildBindingsParameters($abstract, $concrete, $parameters);
        return $this->createBinding($abstract, $concrete, $parameters, true);
    }

    /**
     * Bind a value into the class builder
     * 
     * @param string $abstract 
     * @param mixed $value 
     * @return void 
     */
    public function instance(string $abstract, $value)
    {
        $this->setBindings(
            $abstract,
            [
                '__value' => true,
                '__construct' => $value
            ]
        );
    }

    /**
     * Alias a type to a different name.
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     *
     * @throws \LogicException
     */
    public function alias(string $abstract, string $alias)
    {
        if ($alias === $abstract) {
            throw new LogicException("[{$abstract}] is aliased to itself.");
        }
        $this->aliases['concretes'][$alias] = $abstract;
        $this->aliases['abstracts'][$abstract][] = $alias;
    }

    /**
     * Add a contextual binding to the container.
     *
     * @param  string  $concrete
     * @param  string  $abstract
     * @param  \Closure|string  $implementation
     * @return void
     */
    public function contextual($concrete, $abstract, $implementation)
    {
        $contextualBindings[$this->getAlias($abstract)] = $implementation;
        $this->setBindings($concrete, $contextualBindings, true);
    }

    /**
     * 
     * @param string $name 
     * @param bool $contextual 
     * @return array 
     */
    public function getBindings(?string $name = null, $contextual = false)
    {
        if (null === $name) {
            return $this->bindings;
        }
        if (empty($bindings = $this->bindings[$this->getAlias($name)] ?? [])) {
            return $bindings;
        }
        return $contextual ? $bindings['__contextual'] ?? [] : $bindings;
    }

    private function setBindings(string $name, array $value, $contextual = false)
    {
        if ($contextual) {
            $this->bindings[$name]['__contextual'] = $value;
        } else {
            $this->bindings[$name] = array_merge($this->bindings[$name] ?? [], $value);
        }
    }
}