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

namespace Drewlabs\Support\MethodOverload;

use ArrayIterator;
use Drewlabs\Contracts\Support\OverloadedPartialMethodHandler;
use Drewlabs\Support\Types\AbstractTypes;
use Drewlabs\Support\Types\FuncArgumentEnum;
use Drewlabs\Contracts\Support\FuncArgument as FuncArgumentInterface;
use Drewlabs\Contracts\Support\NamedFuncArgument as SupportNamedFuncArgument;
use Drewlabs\Support\MethodOverload\OverloadMethodArgumentsContainer;
use Drewlabs\Support\Types\FuncArgument;
use Drewlabs\Support\Types\NamedFuncArgument;
use InvalidArgumentException;
use ReflectionFunction;

/** @package Drewlabs\Support */
class OverloadedMethodHandler implements OverloadedPartialMethodHandler
{
    /**
     * A callable object.
     *
     * @var \Closure
     */
    private $callable;

    /**
     * List of argument passed to the method.
     *
     * @var OverloadMethodArgumentsContainer
     */
    private $arguments = [];

    /**
     * Method can be use as fallback
     * 
     * @var bool
     */
    private $isFallback_ = false;

    public function __construct($signatureOrMethod, $methodOrKey, $object)
    {
        if (!\is_int($methodOrKey)) {
            $this->buildFromSignature($signatureOrMethod, $methodOrKey, $object);
        } elseif (\is_string($signatureOrMethod) && method_exists($object, $signatureOrMethod)) {
            $this->buildUsingMethodReflection($object, $signatureOrMethod);
        } elseif ($signatureOrMethod instanceof \Closure) {
            $this->buildUsingClosureReflection($object, $signatureOrMethod);
        } else {
            throw new \Exception('Unrecognized overloaded method definition.');
        }
    }

    public function matches(array $args = [])
    {
        if (empty($this->arguments->getAll())) {
            // Makes methods that accepts zero argument, as fallback
            $this->isFallback_ = true;
            // The empty argument method matches if and only if the list of passed argument is empty
            return empty($args);
        }
        $value_args_count = \count($args);
        if (($this->arguments->optionalArgumentsCount() === 0) && ($arguments_ = $this->arguments->getAll())) {
            return $value_args_count === count($arguments_) &&
                $this->matchArgumentsToParameters($arguments_, $args);
        } else {
            // Get all the arguments
            $arguments_ = $this->arguments->getAll();
            $total_argument_count = count($arguments_);
            if ($value_args_count > $this->arguments->requiredArgumentsCount()) {
                $arguments_ = $value_args_count > $total_argument_count ?
                    array_slice($arguments_, 0, $total_argument_count) :
                    array_slice($arguments_, 0, $value_args_count);
            }
            return $this->matchArgumentsToParameters($arguments_, $args);
        }
    }

    public function isFallback()
    {
        return $this->isFallback_;
    }

    /**
     * {@inheritDoc}
     */
    public function getArguments()
    {
        return array_values(
            array_map(
                static function ($arg) {
                    return (string) $arg;
                },
                $this->arguments->getAll()
            )
        );
    }



    public function getOptionalArguments()
    {
        return array_values(
            array_map(
                static function ($arg) {
                    return (string) $arg;
                },
                $this->arguments->getOptionalArguments() ?? []
            )
        );
    }

    public function getRequiredArguments()
    {
        return array_values(
            array_map(
                static function ($arg) {
                    return (string) $arg;
                },
                $this->arguments->getRequiredArguments() ?? []
            )
        );
    }

    public function call($args)
    {
        return $this->callable->__invoke(...$args);
    }

    private function buildFromSignature($signature, $method, $object)
    {
        $this->callable = $this->bindCallable($object, $method);
        $this->arguments = $this->mapArraySignature($signature);
    }

    private function buildUsingMethodReflection($object, $method)
    {
        $this->callable = $this->bindCallable($object, $method);
        $reflected = new \ReflectionMethod($object, $method);
        // Added set accessible to make the method callable using invoke
        // even though the method were declared protected or private
        $reflected->setAccessible(true);
        $this->arguments = $this->mapArguments($reflected);
    }

    private function bindCallable($object, $method)
    {
        $closure = function (...$args) use ($method) {
            return $this->{$method}(...$args);
        };

        return $closure->bindTo($object, $object);
    }

    private function buildUsingClosureReflection($object, $closure)
    {
        $this->callable = $closure->bindTo($object);
        $reflected = new \ReflectionFunction($closure);
        $this->arguments = $this->mapArguments($reflected);
    }

    /**
     * 
     * @param \ReflectionFunctionAbstract $reflectionFunction 
     * @return OverloadMethodArgumentsContainer 
     */
    private function mapArguments($reflectionFunction)
    {
        // TODO : Initialize values
        $optinal_args_count = 0;
        $required_args_count = 0;
        $optional_arguments = [];
        $required_arguments = [];
        // #endregion Initialize values
        foreach ($reflectionFunction->getParameters() as $curr) {
            $arg = new NamedFuncArgument(
                $curr->getName(),
                !$curr->hasType() ? AbstractTypes::ANY : $curr->getType()->getName(),
                $curr->isOptional() ? FuncArgumentEnum::OPTIONAL : FuncArgumentEnum::REQUIRED
            );
            if ($curr->isOptional()) {
                $optinal_args_count += 1;
                $optional_arguments[] = $arg;
            }
            if (!$curr->isOptional()) {
                $required_args_count += 1;
                $required_arguments[] = $arg;
            }
        }
        return new OverloadMethodArgumentsContainer([
            'all' => $this->normalizeTypes([...$required_arguments, ...$optional_arguments]),
            'required' => $required_arguments,
            'optional' => $optional_arguments,
            'required_count' => $required_args_count,
            'optional_count' => $optinal_args_count,
        ]);
    }

    /**
     * 
     * @param array $signature 
     * @return OverloadMethodArgumentsContainer 
     */
    private function mapArraySignature(array $signature = [])
    {
        // TODO : Initialize values
        $optinal_args_count = 0;
        $required_args_count = 0;
        $required_arguments = [];
        $optional_arguments = [];
        // #endregion Initialize values
        foreach ($signature as $curr) {
            $type = AbstractTypes::ANY;
            $state = FuncArgumentEnum::REQUIRED;
            if (\is_string($curr)) {
                // Argument is required
                $type = $curr;
            } elseif (\is_array($curr)) {
                $type = AbstractTypes::ANY;
                $total_items = \count($curr);
                if ($total_items > 0) {
                    $type = $curr[0] ?? AbstractTypes::ANY;
                    if (($total_items > 1 ? ($curr[1] ?? FuncArgumentEnum::OPTIONAL) : FuncArgumentEnum::REQUIRED) === FuncArgumentEnum::OPTIONAL) {
                        $state = FuncArgumentEnum::OPTIONAL;
                    }
                }
            } else {
                // Argument is of type any and is optional
                $type = AbstractTypes::ANY;
                $state = FuncArgumentEnum::OPTIONAL;
            }
            $funcArg = new FuncArgument($type, $state);
            if ($funcArg->isOptional()) {
                $optinal_args_count += 1;
                $optional_arguments[] = $funcArg;
            }
            if (!$funcArg->isOptional()) {
                $required_args_count += 1;
                $required_arguments[] = $funcArg;
            }
            $carr[] = $funcArg;
        }
        return new OverloadMethodArgumentsContainer([
            'all' => $this->normalizeTypes([...$required_arguments, ...$optional_arguments]),
            'required' => $required_arguments,
            'optional' => $optional_arguments,
            'required_count' => $required_args_count,
            'optional_count' => $optinal_args_count,
        ]);
    }

    /**
     * Undocumented function.
     *
     * @param FuncArgumentInterface[] $types
     *
     * @return FuncArgumentInterface
     */
    private function normalizeTypes($types)
    {
        return array_map(static function (FuncArgumentInterface $type) {
            switch ($type->getType()) {
                case 'int':
                    return new NamedFuncArgument(
                        $type instanceof SupportNamedFuncArgument ? $type->getName() : '*',
                        AbstractTypes::INTEGER,
                        $type->isOptional() ? FuncArgumentEnum::OPTIONAL : FuncArgumentEnum::REQUIRED
                    );
                case 'bool':
                    return new NamedFuncArgument(
                        $type instanceof SupportNamedFuncArgument ? $type->getName() : '*',
                        AbstractTypes::BOOLEAN,
                        $type->isOptional() ? FuncArgumentEnum::OPTIONAL : FuncArgumentEnum::REQUIRED
                    );
                default:
                    return $type;
            }
        }, $types);
    }

    /**
     * 
     * @param FuncArgument[] $arguments 
     * @param array $params 
     * @return bool 
     * @throws InvalidArgumentException 
     */
    private function matchArgumentsToParameters(array $arguments, array $params)
    {
        return array_reduce(
            drewlabs_core_array_zip($params, $arguments),
            function ($isMatch, $argAndType) {
                [$arg, $type] = $argAndType;
                if (null === $type) {
                    return null === $arg ? $isMatch && true : $isMatch && false;
                } else {
                    $type_class = \gettype($arg);
                    $arg_class = $type->getType();
                    $is_arg_instance_of = $arg instanceof $arg_class;
                    $arg_null_for_optional = null === $arg && $type->isOptional();
                    return $isMatch && ($arg_null_for_optional ||
                        AbstractTypes::ANY === $arg_class ||
                        $type_class === $arg_class ||
                        $is_arg_instance_of);
                }
            },
            true
        );
    }
}
