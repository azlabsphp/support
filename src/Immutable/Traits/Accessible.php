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

namespace Drewlabs\Support\Immutable\Traits;

use Drewlabs\Support\Immutable\Exceptions\ImmutableObjectException;

trait Accessible
{
    public function __isset($name)
    {
        return isset($this->___attributes[$name]);
    }

    public function __unset($name)
    {
        throw new ImmutableObjectException(__CLASS__);
    }

    /**
     * {@inheritDoc}
     */
    public function __clone()
    {
        if ($this->___attributes) {
            $this->___attributes = clone $this->___attributes;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->___attributes->offsetExists($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        if (\is_int($offset)) {
            return;
        }

        return $this->__get($offset);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ImmutableObjectException Use the {copyWith} method to create
     *                                  a new object from the properties of the current object while changing the
     *                                  needed properties
     */
    public function offsetSet($offset, $value)
    {
        throw new ImmutableObjectException(__CLASS__);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ImmutableObjectException Use the {copyWith} method to create
     *                                  a new object from the properties of the current object while changing the
     *                                  needed properties to null
     */
    public function offsetUnset($offset)
    {
        throw new ImmutableObjectException(__CLASS__);
    }
}