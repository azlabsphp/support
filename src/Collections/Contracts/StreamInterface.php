<?php

namespace Drewlabs\Support\Collections\Contracts;

interface StreamInterface extends Collectable
{

    /**
     * Defines a transformation method to apply on each stream data
     * 
     * @param callable|\Closure $callback 
     * @return self|ArrayableInterface
     */
    public function map(callable $callback);

    /**
     * Set a reducer that should be applied to a stream data
     * 
     * @param mixed $identity 
     * @param \Closure<R,T,R> $reducer 
     * @return mixed 
     * @throws Exception 
     */
    public function reduce($identity, callable $reducer);

    /**
     * Apply filtering to the stream using a predicate function
     * 
     * @param callable $predicate
     * 
     * @return self|ArrayableInterface
     */
    public function filter(callable $predicate);

    /**
     * Set a limit on the number of stream data
     * 
     * @param int $n 
     * @return $this 
     */
    public function take(int $n);

    /**
     * Operator to process a stream data until a condition is met
     * 
     * @param callable|mixed $value 
     * @return self|ArrayableInterface
     */
    public function takeUntil($value);

    /**
     * Takes stream data while a value is true
     * 
     * By default, stream is dropped if flexible=NULL|false. Else the only data
     * not matching the condition are dropped.
     * 
     * Note: Becareful when running in flexible mode, to avoid undesireable results
     * 
     * @param mixed $value 
     * @param bool $flexible 
     * @return self|ArrayableInterface
     */
    public function takeWhile($value, $flexible = true);

    /**
     * Set an offset on the number of stream data
     * 
     * @param mixed $n 
     * @return self|ArrayableInterface 
     */
    public function skip(int $n);

    /**
     * Method to apply an executor to each item in the stream
     * 
     * @param callable $callback 
     * @return void 
     */
    public function each(callable $callback);

    /**
     * Returns the first element of the stream or the default value if missing
     * 
     * @param callable|mixed $default 
     * @return mixed 
     */
    public function firstOr($default = null);

    /**
     * Returns the first element of the stream
     * 
     * @return mixed 
     */
    public function first();
}