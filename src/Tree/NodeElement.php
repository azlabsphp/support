<?php

namespace Drewlabs\Support\Tree;

interface NodeElement
{
    /**
     * The primary key of the node element
     * 
     * @return string|int 
     */
    public function key();

    /**
     * Checks if the node is a root node
     * 
     * @return bool 
     */
    public function isRoot();

    /**
     * returns the parent node of the current node
     * 
     * @return self 
     */
    public function previous();
}