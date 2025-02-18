<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Support\Traits;

trait MethodProxy
{
    use ObjectMethodsProxy;

    /**
     * Provide a dynamic method call interface to the current object.
     * if the specified method does not exists, the default method is called
     * instead.
     *
     * @param mixed $method
     * @param array $args
     *
     * @return mixed
     */
    public function call($method, $args = [], ?\Closure $default = null)
    {
        if ($method instanceof \Closure) {
            try {
                return (new \ReflectionFunction($method))->invoke(...$args);
            } catch (\Error|\BadMethodCallException|\ReflectionException $e) {
                // Call the default method if the specified method does not exits
                if ((null !== $default) && \is_callable($default)) {
                    return $default(...$args);
                }
            }
        }

        return $this->forwardCallTo($this, $method, $args, $default);
    }
}
