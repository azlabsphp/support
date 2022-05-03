<?php

namespace Drewlabs\Support\Collections\Contracts;

interface Arrayable
{
    /**
     * Returns the array representation of the underlying object
     * 
     * @return array 
     */
    public function toArray();
}