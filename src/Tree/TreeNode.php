<?php

namespace Drewlabs\Support\Tree;

use JsonSerializable;

interface TreeNode extends JsonSerializable, NodeElement
{

    /**
     * Node children setter and getter interface
     * 
     * @param self[]|null $values
     * 
     * @return self[]
     */
    public function childNodes(array $values = null);

    /**
     * Getter and Setter Zero based level of the node in the tree structure
     * 
     * @param string|int|null $value
     * 
     * @return int
     */
    public function level($value = null);

}