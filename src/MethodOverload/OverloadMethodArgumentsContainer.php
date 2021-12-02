<?php

namespace Drewlabs\Support\MethodOverload;

use Drewlabs\Support\Immutable\ValueObject;
use Drewlabs\Contracts\Support\FuncArgument;

class OverloadMethodArgumentsContainer extends ValueObject
{

    protected function getJsonableAttributes()
    {
        return [
            'all',
            'required',
            'optional',
            'required_count',
            'optional_count',
        ];
    }

    public function count()
    {
        return count($this->getAll() ?? []);
    }

    public function length()
    {
        return $this->count();
    }

    /**
     * 
     * @return FuncArgument[] 
     */
    public function getAll()
    {
        return $this->all;
    }

    /**
     * 
     * @return FuncArgument[] 
     */
    public function getRequiredArguments()
    {
        return $this->required;
    }

    /**
     * 
     * @return FuncArgument[] 
     */
    public function getOptionalArguments()
    {
        return $this->optional;
    }

    /**
     * 
     * @return int 
     */
    public function requiredArgumentsCount()
    {
        return $this->required_count;
    }

    /**
     * 
     * @return int 
     */
    public function optionalArgumentsCount()
    {
        return $this->optional_count;
    }
}
