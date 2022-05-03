<?php

namespace Drewlabs\Support\Tree;

use Drewlabs\Support\Tree\Traits\Node as TraitsNode;

/** @package Drewlabs\Support\Tree */
class Node implements TreeNode
{
    use TraitsNode;

    /**
     * 
     * @param mixed $key 
     * @param mixed $data 
     * @param int|string|TreeNode|null $parent 
     */
    public function __construct(
        $key,
        $data = null,
        $parent = null,
    ) {
        $this->__KEY__ = $key;
        $this->__STATE__ = $data;
        $this->__PARENT__ = $parent instanceof TreeNode ?
            $parent->key() :
            $parent;
    }
}
