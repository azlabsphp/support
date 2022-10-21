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

use Drewlabs\Support\Collections\HigherOrderProxy;
use Drewlabs\Support\Collections\HigherOrderWhenProxy;

if (!function_exists('drewlabs_core_create_get_callback')) {
    /**
     * Get a value retrieving callback.
     *
     * @param callable|string|null $value
     *
     * @return callable
     */
    function drewlabs_core_create_get_callback($value)
    {
        if (drewlabs_core_is_closure($value)) {
            return $value;
        }

        return static function ($item) use ($value) {
            return drewlabs_core_get($item, $value);
        };
    }
}

if (!function_exists('drewlabs_core_create_evaluation_callback')) {
    /**
     * Create an operator checker callback.
     *
     * @param string      $key
     * @param string|null $operator
     * @param mixed       $value
     *
     * @return \Closure
     */
    function drewlabs_core_create_evaluation_callback($key, $operator = null, $value = null)
    {
        if (1 === func_num_args()) {
            $value = true;
            $operator = '=';
        }
        if (2 === func_num_args()) {
            $value = $operator;
            $operator = '=';
        }

        return static function ($item) use ($key, $operator, $value) {
            $value_ = drewlabs_core_get($item, $key);
            $strings_ = array_filter(
                [$value_, $value],
                static function ($value) {
                    return is_string($value) || (is_object($value) && method_exists($value, '__toString'));
                }
            );
            $objects_ = array_filter([$value_, $value], 'is_object');
            if (count($strings_) < 2 && 1 === count($objects_)) {
                return in_array($operator, ['!=', '<>', '!=='], true);
            }
            switch ($operator) {
                default:
                case '=':
                case '==':
                    return $value_ === $value;
                case '!=':
                case '<>':
                    return $value_ !== $value;
                case '<':
                    return $value_ < $value;
                case '>':
                    return $value_ > $value;
                case '<=':
                    return $value_ <= $value;
                case '>=':
                    return $value_ >= $value;
                case '===':
                    return $value_ === $value;
                case '!==':
                    return $value_ !== $value;
            }
        };
    }
}

if (!function_exists('drewlabs_core_create_when_proxy_callback')) {
    /**
     * @param mixed $collection
     * @param mixed $condition
     *
     * @return Closure&#Function#3b7abf60
     */
    function drewlabs_core_create_when_proxy_callback($collection, $condition)
    {
        return new HigherOrderWhenProxy($collection, $condition);
    }
}

if (!function_exists('drewlabs_core_high_order_proxy_callback')) {
    /**
     * Creates a high order proxy callback to a Collection item.
     *
     * @param mixed $collection
     * @param mixed $method
     *
     * @return Closure&#Function#427aca65
     */
    function drewlabs_core_high_order_proxy_callback($collection, $method)
    {
        return new HigherOrderProxy($collection, $method);
    }
}
