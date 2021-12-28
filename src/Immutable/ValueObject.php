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

namespace Drewlabs\Support\Immutable;

use Drewlabs\Contracts\Clonable;
use Drewlabs\Contracts\Support\Immutable\ValueObjectInterface;
use Drewlabs\Support\Compact\PhpStdClass;
use Drewlabs\Support\Immutable\Traits\ValueObject as TraitsValueObject;

abstract class ValueObject implements ValueObjectInterface, Clonable, \IteratorAggregate
{
    use TraitsValueObject;

    /**
     * Provides an object oriented iterator over the this object keys and values.
     *
     * @return \Traversable
     */
    public function each(\Closure $callback)
    {
        return $this->getAttributes()->each($callback);
    }

    /** @return \Traversable  */
    public function getIterator(): \Traversable
    {
        foreach ($this->getAttributes() as $key => $value) {
            yield $key => $value;
        }
    }

    public static function hiddenProperty()
    {
        return '___hidden';
    }

    public static function guardedProperty()
    {
        return '___guarded';
    }

    /**
     * return this list of dynamic attributes that can be set on the ihnerited class.
     *
     * @return array
     */
    abstract protected function getJsonableAttributes();

    /**
     * @return PhpStdClass|mixed
     */
    final protected function getAttributes()
    {
        return $this->___attributes;
    }
}
