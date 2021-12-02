<?php

namespace Drewlabs\Support\Exceptions;

use Exception;

class InvalidTypeException extends Exception
{
    public function __construct(string $property, string $expected, string $got)
    {
        parent::__construct("Wrong type for $property, Expected $expected, Got: $got");
    }
}
