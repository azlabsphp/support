<?php

namespace Drewlabs\Support\DI\Exceptions;

use Exception;

class TypeBindingException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }  
}