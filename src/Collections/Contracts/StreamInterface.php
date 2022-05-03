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

namespace Drewlabs\Support\Collections\Contracts;

interface StreamInterface extends Collectable
{
    /**
     * Defines a transformation method to apply on each stream data.
     *
     * @param callable|\Closure $callback
     *
     * @return self|Arrayable
     */
    public function map(callable $callback);

    /**
     * Set a reducer that should be applied to a stream data. If the identity value
     * is omitted, meaning if the only a single parameter is passed, the parameter is
     * consider to be the reducer function.
     * 
     * ```php
     * 
     * $result = $stream->take(10)
     *              ->reduce(static function ($carry, $current) {
     *                      $carry += $current;
     *                      return $carry;
     *              });
     * 
     * // Is same as
     * $result = $stream->take(10)
     *              ->reduce(0, static function ($carry, $current) {
     *                      $carry += $current;
     *                      return $carry;
     *              });
     * ```
     *
     * @param mixed|callable           $identityOrFunc
     * @param \Closure<R,T,R>|null $reducer
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function reduce($identityOrFunc, callable $reducer = null);

    /**
     * Apply filtering to the stream using a predicate function.
     *
     * @return self|Arrayable
     */
    public function filter(callable $predicate);

    /**
     * Set a limit on the number of stream data.
     *
     * @return $this
     */
    public function take(int $n);

    /**
     * Operator to process a stream data until a condition is met.
     *
     * @param callable|mixed $value
     *
     * @return self|Arrayable
     */
    public function takeUntil($value);

    /**
     * Takes stream data while a value is true.
     *
     * By default, stream is dropped if flexible=NULL|false. Else the only data
     * not matching the condition are dropped.
     *
     * Note: Becareful when running in flexible mode, to avoid undesireable results
     *
     * @param mixed $value
     * @param bool  $flexible
     *
     * @return self|Arrayable
     */
    public function takeWhile($value, $flexible = true);

    /**
     * Set an offset on the number of stream data.
     *
     * @param mixed $n
     *
     * @return self|Arrayable
     */
    public function skip(int $n);

    /**
     * Method to apply an executor to each item in the stream.
     *
     * @return void
     */
    public function each(callable $callback);

    /**
     * Returns the first element of the stream or the default value if missing.
     *
     * @param callable|mixed $default
     *
     * @return mixed
     */
    public function firstOr($default = null);

    /**
     * Returns the first element of the stream.
     *
     * @return mixed
     */
    public function first();
}
