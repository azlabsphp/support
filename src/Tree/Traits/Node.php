<?php

namespace Drewlabs\Support\Tree\Traits;

trait Node
{
    /**
     * 
     * @var int
     */
    private $__KEY__;

    /**
     * 
     * @var int|string
     */
    private $__PARENT__;

    /**
     * 
     * @var NodeInterface[]
     */
    private $__CHILDREN__ = [];

    /**
     * 
     * @var int
     */
    private $__LEVEL__;

    /**
     * 
     * @var mixed
     */
    private $__STATE__;

    public function key()
    {
        return $this->__KEY__;
    }

    public function previous()
    {
        return $this->__PARENT__;
    }
    
    public function isRoot()
    {
        return null === $this->__PARENT__;
    }

    public function childNodes(array $values = null)
    {
        if (null !== $values) {
            $this->__CHILDREN__ = $values;
        }
        return $this->__CHILDREN__;
    }

    public function level($value = null)
    {
        if (null !== $value) {
            $this->__LEVEL__ = $value;
        }
        return $this->__LEVEL__;
    }

    public function __toString()
    {
        return $this->__KEY__;
    }

    public function value($state = null)
    {
        if (null !== $state) {
            $this->__STATE__ = $state;
        }
        return $this->__STATE__;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->__STATE__;
    }
}
