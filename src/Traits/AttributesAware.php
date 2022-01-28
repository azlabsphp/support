<?php

namespace Drewlabs\AuthHttpGuard\Traits;

use Drewlabs\Core\Helpers\Arr;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

trait AttributesAware
{

    /**
     * 
     * @var array<string|mixed>
     */
    private $attributes = [];

    /**
     * 
     * @param array $attributes 
     * @return mixed 
     */
    public static function createFromAttributes(array $attributes)
    {

        $reflector = new ReflectionClass(__CLASS__);
        if ($reflector->isAbstract()) {
            throw new LogicException("Class is not instanciable...");
        }
        if ($reflector->isInstantiable()) {
            return static::createNewArgsInstance($reflector, $attributes);
        }
        try {
            return static::createByReflectedConstructor($reflector, $attributes);
        } catch (ReflectionException $e) {
            return new self;
        }
    }

    private static function validateConstructorFirstParameter(ReflectionParameter $parameter)
    {
        $type = $parameter->getType();
        if (
            ($type instanceof ReflectionNamedType && $type->getName() === 'array') ||
            !$parameter->hasType()
        ) {
            return;
        }
        throw new LogicException(__CLASS__ . " must have only one required parameter which must be of type array");
    }

    private static function validateConstructorLeastParameters(array $parameters = [])
    {
        foreach ($parameters as $parameter) {
            if (!$parameter->isOptional()) {
                throw new LogicException(__CLASS__ . " must have only one required parameter which must be of type array");
            }
        }
    }

    private static function createNewArgsInstance(ReflectionClass $reflector, array $attributes = [])
    {
        $constructor = $reflector->getConstructor();
        if (null === $constructor) {
            return new static;
        }
        $parameters = $constructor->getParameters();
        static::validateConstructorFirstParameter($parameters[0]);
        if (count($parameters) !== 1) {
            static::validateConstructorLeastParameters(array_slice($parameters, 1));
        }
        return $reflector->newInstanceArgs([$attributes]);
    }

    private static function createByReflectedConstructor(ReflectionClass $reflector, array $attributes = [])
    {
        $constructor = $reflector->getConstructor();
        if (null === $constructor) {
            return new static;
        }
        $parameters = $constructor->getParameters();
        static::validateConstructorFirstParameter($parameters[0]);
        if (count($parameters) !== 1) {
            static::validateConstructorLeastParameters(array_slice($parameters, 1));
        }
        $constructor->setAccessible(true);
        $object = $reflector->newInstanceWithoutConstructor();
        $constructor->getClosure($object)->__invoke($attributes);
        return $object;
    }

    public function __set(string $name, $value)
    {
        Arr::set($this->attributes, $name, $value);
    }

    public function __get($name)
    {
        return Arr::get($this->attributes ?? [], $name, null);
    }
}
