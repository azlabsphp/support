<?php

namespace Drewlabs\Support\DI\Exceptions;

use Exception;

class TypeResolutionException extends Exception
{
    public function __construct($concrete, string $abstract = null)
    {
        $name = is_string($concrete) ? $concrete : get_class();
        $message = is_null($abstract) ? "Target [$name] is not instantiable." : "Target [$abstract] is not instantiable while building [$name].";
        parent::__construct($message);
    }
}