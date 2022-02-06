<?php

namespace Drewlabs\Support\Collections\Contracts;

use Traversable;

interface CollectorInterface
{
    /**
     * Provides implementation that Creates a Ds from the stream output
     * 
     * @param Traversable $source 
     * @return mixed 
     */
    public function __invoke(\Traversable $source);
}