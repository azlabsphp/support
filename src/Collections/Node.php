<?php

namespace Drewlabs\Support\Collections;

class Node
{
    /**
     * 
     * @var mixed
     */
    public $value;

    /**
     * Pointer to the next node
     * 
     * @var self
     */
    public $next;

    /**
     * Pointer to the previous node
     * 
     * @var self
     */
    public $previous;

    public function __construct($value)
    {
        $this->value = $value;
    }
}