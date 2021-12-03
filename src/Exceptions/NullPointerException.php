<?php

namespace Drewlabs\Support\Exceptions;

use Exception;

class NullPointerException extends Exception
{
    public function __construct(string $property)
    {
        parent::__construct("ERROR : $property value is not initialized");
    }
}