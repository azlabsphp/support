<?php

namespace Drewlabs\Support\DI;

interface ContextualBindingsAware
{
    /**
     * 
     * @param mixed $concrete 
     * @return ContextualBindingsBuilder 
     */
    public function when($concrete);
}