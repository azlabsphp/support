<?php

namespace Drewlabs\Support\DI;

interface ContextualBindingsBuilder
{
    /**
     * 
     * @param string $abstract 
     * @return self 
     */
    public function require($abstract);

    /**
     * 
     * @param string|\Closure|array $implementation 
     * @return void 
     */
    public function provide($implementation);
}