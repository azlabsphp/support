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

namespace Drewlabs\Support\Traits;

/**
 * Provides a more dynamic proxy trait for calling dynamic method on object.
 * A default method call is added to be called if the method that not exists on the
 * proxied object.
 *
 * {@copyright 2011-2021 Laravel LLC. <https://laravel.com/>}
 */
trait ObjectMethodsProxy
{
    public function forwardCallTo($object, $method, $args = [], ?\Closure $default = null)
    {
        return $this->proxy($object, $method, $args, $default);
    }

    public function proxy($object, $method, $args = [], ?\Closure $default = null)
    {
        try {
            // Call the method on the provided object
            return $object->{$method}(...$args);
        } catch (\Error | \BadMethodCallException $e) {
            // Call the default method if the specified method does not exits
            if ((null !== $default) && \is_callable($default)) {
                return $default(...$args);
            }
            $pattern = '~^Call to undefined method (?P<class>[^:]+)::(?P<method>[^\(]+)\(\)$~';
            if (!preg_match($pattern, $e->getMessage(), $matches)) {
                throw $e;
            }
            if (
                $matches['class'] !== \get_class($object) ||
                $matches['method'] !== $method
            ) {
                throw $e;
            }
            throw new \BadMethodCallException(
                sprintf(
                    'Call to undefined method %s::%s()',
                    static::class,
                    $method
                )
            );
        }
    }
}
