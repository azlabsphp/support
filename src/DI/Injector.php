<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Support\DI;

use Closure;
use Drewlabs\Support\DI\Exceptions\TypeBindingException;
use Drewlabs\Support\DI\Exceptions\TypeResolutionException;
use Drewlabs\Support\DI\Traits\BindginsResolversAware;
use Drewlabs\Support\DI\Traits\BindingsProvidersAware;
use Psr\Container\ContainerInterface;

final class Injector implements \ArrayAccess, ContainerInterface, ContextualBindingsAware
{
    use BindginsResolversAware;
    use BindingsProvidersAware;

    /**
     * @var array<int,string>
     */
    private const PHP_TYPE = [
        'boolean',
        'integer',
        'double',
        'string',
        'resource',
        'resource (closed)',
        'object',
        'array',
    ];

    /**
     * If true, the Injector will try to load classes
     * decorated with AutoResolve attributes.
     *
     * @var bool
     */
    public static $autoload = false;

    /**
     * Object bindings.
     *
     * @var array<string,array<int,mixed>>
     */
    private $bindings = [];

    /**
     * @var array<string,array<int,string>>
     */
    private $aliases = [
        /*
         * List of interfaces/abstracts to implementations/concretes
         */
        'abstracts' => [],
        /*
         * List of  implementations/concretes to interfaces/abstracts
         */
        'concretes' => [],
    ];

    /**
     * Class singleton.
     *
     * @var self
     */
    private static $instance;

    private function __construct()
    {
        if (class_exists(AutoResolve::class)) {
            foreach ($this->getAutoResolvableClasses(get_declared_classes()) as $key => $value) {
                // TODO : Register auto resolvable classes
            }
        }
    }

    public function when($concrete)
    {
        $list = [];
        foreach ($this->wrapToArray($concrete) as $value) {
            $list[] = $this->getAlias($value);
        }

        return new ContextualBuilder($this, $list);
    }

    public function has(string $name): bool
    {
        return $this->offsetExists($name);
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function getParameterClassName(\ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        if (null !== ($class = $parameter->getDeclaringClass())) {
            if ('self' === $name) {
                return $class->getName();
            }

            if ('parent' === $name && $parent = $class->getParentClass()) {
                return $parent->getName();
            }
        }

        return $name;
    }

    /**
     * @param bool $singleton
     *
     * @throws TypeBindingException
     *
     * @return void
     */
    private function createBinding(
        string $abstract,
        \Closure $concrete,
        array $parameters = [],
        $singleton = false
    ) {
        $this->dropStaleInstances($abstract);
        $this->bindClosure($abstract, $concrete, $parameters, $singleton);
    }

    private function bindClosure(
        string $abstract,
        \Closure $callback,
        array $parameters = [],
        bool $singleton = false
    ) {
        $this->setBindings($abstract, [
            '__singleton' => $singleton,
            '__construct' => [
                $callback,
                $parameters,
            ],
        ]);
    }

    private function build(string $name)
    {
        $dependences = [];
        $reflector = new \ReflectionClass($name);

        if (!$reflector->isInstantiable()) {
            return $this->notInstantiable($name);
        }
        if (null === $reflector) {
            return new $name();
        }
        $constructor = $reflector->getConstructor();
        $this->resolveDependencies(
            $name,
            $constructor->getParameters(),
            $dependences
        );
        if (empty($this->getBindings($name))) {
            $this->setBindings($name, [
                '__singleton' => false,
                '__construct' => [$name, array_keys($dependences)],
            ]);
        }

        return $reflector->newInstanceArgs(array_values($dependences));
    }

    private function resolveDependencies(string $name, array $parameters, &$dependences)
    {
        $index = 0;
        foreach ($parameters as $parameter) {
            ++$index;
            $type = self::getParameterClassName($parameter);
            if (null === $type) {
                $dependences[$type] = $this->resolvePrimitive($parameter);
                continue;
            }
            if ($parameter->isVariadic()) {
                // TODO : Resolve variadic parameter
                $dependences[$type] = $this->resolveVariadicClass($parameter);
            }
            if (!empty($parameters_ = $this->getBindings($type)) && isset($parameters_['__construct'])) {
                $dependences[$type] = $this->createClassUsingBindings($type, $parameters_);
                continue;
            }
            if (class_exists($type)) {
                $dependences[$type] = $this->build($type);
                continue;
            }
            throw new TypeResolutionException(\get_class($parameter), $name);
        }
    }

    private function createClassUsingBindings(string $name, array $bindings)
    {
        if ((false !== ($bindings['__value'] ?? false))) {
            return $bindings['__construct'];
        }
        if ((false !== ($bindings['__singleton'] ?? false)) && !\is_array($bindings['__construct'] ?? null)) {
            return $bindings['__construct'];
        }
        if (\is_string($definition = $bindings['__construct'][0] ?? null) && class_exists($definition)) {
            $object = $this->createNewClass(
                $definition,
                iterator_to_array(
                    $this->resolveParameters($bindings['__construct'][1] ?? [])
                )
            );
            if ($bindings['__singleton']) {
                $this->setBindings($name, array_merge(
                    $this->getBindings($name),
                    ['__construct' => $object]
                ));
            }

            return $object;
        }
        if (($closure = $bindings['__construct']) && ($definition = $closure[0] ?? null) && ($definition instanceof \Closure)) {
            $object = $this->resolveClosure(...$closure);
            if ($bindings['__singleton']) {
                $this->setBindings($name, array_merge(
                    $this->getBindings($name),
                    ['__construct' => $object]
                ));
            }

            return $object;
        }
    }

    private function resolveParameters(array $parameters)
    {
        foreach ($parameters as $parameter) {
            if (!\is_string($parameter)) {
                yield $parameter;
                continue;
            }
            yield !empty($value = $this->getBindings($parameter)) && isset($value['__construct']) ?
                $this->createClassUsingBindings($parameter, $value) :
                $this->build($parameter);
        }
    }

    private function createNewClass(string $class, ?array $parameters = [])
    {
        return empty($parameters = $parameters ?? []) ?
            $this->build($class) :
            new $class(...$parameters);
    }

    private function resolveClosure(\Closure $callback, ?array $parameters = [])
    {
        return $callback->__invoke($this, $parameters);
    }

    private function wrapToArray($value)
    {
        return \is_array($value) ? $value : [$value];
    }

    private function getAutoResolvableClasses($list)
    {
        foreach ($list as $abstract) {
            if (\array_key_exists($abstract, $this->bindings ?? [])) {
                continue;
            }
            if (!class_exists($abstract)) {
                continue;
            }
            $reflection = new \ReflectionClass($abstract);
            $attributes = array_map(static function (\ReflectionAttribute $attribute) {
                return $attribute->getName();
            }, $reflection->getAttributes());
            if (!\in_array(AutoResolve::class, $attributes, true)) {
                continue;
            }
            yield $abstract;
        }
    }

    private function resolve(string $name, $parameters = [])
    {
        if (!empty($value = $this->getBindings($name)) && isset($value['__construct'])) {
            return $this->createClassUsingBindings($name, $value);
        }

        return $this->build($name);
    }

    /**
     * Resolve a class based variadic dependency from the container.
     *
     * @return mixed
     */
    private function resolveVariadicClass(\ReflectionParameter $parameter)
    {
        // TODO : Implements variadic resolver
        // $name = self::getParameterClassName($parameter);
        // $abstract = $this->getAlias($name);
        // if (empty($abstractAlias = $this->alias['abstract'][$abstract] ?? [])) {
        //     return;
        // }
        // foreach ($abstractAlias ?? [] as $alias) {
        //     if (! is_null($binding = $this->findInContextualBindings($alias))) {
        //         return $binding;
        //     }
        // }

        // return array_map(function ($abstract) {
        //     return $this->resolve($abstract);
        // }, $concrete);
    }

    private function resolvePrimitive(\ReflectionParameter $parameter)
    {
        if (
            !empty($concrete = $this->getBindings($parameter->getName()))
            && ($concrete['__value'] ?? false)
        ) {
            return $concrete['__construct'] ?? null instanceof \Closure ? $concrete($this) : $concrete;
        }
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        if ($parameter->allowsNull()) {
            return null;
        }
        $this->unresolvablePrimitive($parameter);
    }

    /**
     * Get the Closure to be used when building a type.
     *
     * @param string $abstract
     * @param string $concrete
     *
     * @return \Closure
     */
    private function getClosure($abstract, $concrete)
    {
        return static function (self $container, $parameters = []) use ($abstract, $concrete) {
            if ($abstract === $concrete) {
                return $container->build($concrete);
            }

            return $container->resolve(
                $concrete,
                $parameters
            );
        };
    }

    private function buildBindingsParameters(string $abstract, $concrete = null, array $parameters = [])
    {
        if (null === $concrete) {
            $concrete = $abstract;
        }
        if (!$concrete instanceof \Closure) {
            if (!\is_string($concrete) && !\is_array($concrete)) {
                throw new \TypeError(__METHOD__.': Argument #2 must be of type Closure|string|null');
            }
            if (\is_array($concrete) && \is_string($concrete[0] ?? null)) {
                $concrete = $concrete[0];
                $parameters = \is_array($p = $concrete[1] ?? $parameters ?? []) ?
                    $p :
                    array_filter($this->wrapToArray($p));
            }
            $concrete = $this->getClosure($abstract, $concrete);
        }

        return [$abstract, $concrete, $parameters];
    }

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param string $abstract
     *
     * @return void
     */
    private function dropStaleInstances($abstract)
    {
        unset($this->bindings[$abstract], $this->aliases['concretes'][$abstract]);
    }

    private function unresolvablePrimitive(\ReflectionParameter $parameter)
    {
        $message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";
        throw new TypeBindingException($message);
    }

    private function notInstantiable($concrete, $abstract = null)
    {
        throw new TypeResolutionException($concrete, $abstract);
    }
}
