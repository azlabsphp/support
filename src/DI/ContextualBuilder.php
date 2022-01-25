<?php

namespace Drewlabs\Support\DI;

/** @package Drewlabs\Support\DI */
class ContextualBuilder implements ContextualBindingsBuilder
{
    /**
     * 
     * @var Injector
     */
    private $injector;

    /**
     * 
     * @var string|array
     */
    private $concrete;

    /**
     * 
     * @var string
     */
    private $abstract;

    /**
     * 
     * @param Injector $injector 
     * @param string|array $concrete 
     * @return void 
     */
    public function __construct($injector, $concrete)
    {
        $this->injector = $injector;
        $this->concrete = $concrete;
    }

    public function require($abstract)
    {
        $this->abstract = $abstract;
        return $this;
    }

    public function provide($implementation)
    {
        foreach ($this->wrapToArray($this->concrete) as $value) {
            $this->injector->contextual($value, $this->abstract, $implementation);
        }
    }

    private function wrapToArray($value)
    {
        return is_array($value) ? $value : [$value];
    }
}
