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

namespace Drewlabs\Support\Collections;

use function Drewlabs\Support\Proxy\Collection;

use Iterator;

class LazyCollection implements \IteratorAggregate
{
    /**
     * @var \Iterator|\Iterable
     */
    private $source;

    /**
     * @param \Iterable|\Iterator $values
     *
     * @return self
     */
    public function __construct($values = [])
    {
        if (\is_array($values)) {
            $this->source = new \ArrayIterator($values);
        } elseif ($values instanceof self) {
            $this->source = $this->getIterator();
        } else {
            $this->source = $values;
        }
    }

    public static function empty()
    {
        $self = new static([]);

        return $self;
    }

    /**
     * @throws \Exception
     *
     * @return \Iterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return $this->makeIterator($this->source);
    }

    public function takeUntil($value)
    {
        $callback = drewlabs_core_is_closure($value) ? $value : (static function ($item) use ($value) {
            return $item === $value;
        });

        return new static(function () use ($callback) {
            foreach ($this as $key => $item) {
                if ($callback($item, $key)) {
                    break;
                }
                yield $key => $item;
            }
        });
    }

    /**
     * Chunk the collection into chunks with a callback.
     *
     * @return static
     */
    public function chunkWhile(callable $callback)
    {
        return new static(function () use ($callback) {
            $iterator = $this->getIterator();
            $chunk = Collection();
            if ($iterator->valid()) {
                $chunk[$iterator->key()] = $iterator->current();
                $iterator->next();
            }

            while ($iterator->valid()) {
                if (!$callback($iterator->current(), $iterator->key(), $chunk)) {
                    yield Collection($chunk);
                    $chunk = Collection();
                }
                $chunk[$iterator->key()] = $iterator->current();
                $iterator->next();
            }

            if ($chunk->isNotEmpty()) {
                yield Collection($chunk);
            }
        });
    }

    /**
     * Get all items in the enumerable.
     *
     * @return array
     */
    public function all()
    {
        if (\is_array($this->source)) {
            return $this->source;
        }

        return iterator_to_array($this->getIterator());
    }

    /**
     * Take items in the collection until a given point in time.
     *
     * @return static
     */
    public function takeUntilTimeout(\DateTimeInterface $timeout)
    {
        $timeout = $timeout->getTimestamp();

        return $this->takeWhile(static function () use ($timeout) {
            return drewlabs_core_datetime_is_future($timeout);
        });
    }

    /**
     * Take items in the collection while the given condition is met.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function takeWhile($value)
    {
        $callback = drewlabs_core_is_closure($value) ? $value : (static function ($item) use ($value) {
            return $item === $value;
        });

        return $this->takeUntil(static function ($item, $key) use ($callback) {
            return !$callback($item, $key);
        });
    }

    /**
     * Count the number of items in the collection by a field or using a callback.
     *
     * @param callable|string $countBy
     *
     * @return static
     */
    public function countBy($countBy = null)
    {
        $countBy = null === $countBy
            ? static function ($value) {
                return $value;
            }
            : drewlabs_core_create_get_callback($countBy);

        return new static(function () use ($countBy) {
            $counts = [];
            foreach ($this as $key => $value) {
                $group = $countBy($value, $key);
                if (empty($counts[$group])) {
                    $counts[$group] = 0;
                }
                ++$counts[$group];
            }

            yield from $counts;
        });
    }

    /**
     * Map the values into a new class.
     *
     * TODO: Move to the Enumarable trait
     *
     * @param string $class
     *
     * @return static
     */
    public function mapInto($class)
    {
        return $this->map(static function ($value, $key) use ($class) {
            return new $class($value, $key);
        });
    }

    public function count()
    {
        return \count($this->all());
    }

    /**
     * Apply the transformation callback over each item element.
     *
     * @param Closure|callable $callback
     *
     * @return self
     */
    public function map($callback, bool $preserveKey = true)
    {
        if (!($callback instanceof \Closure) || !\is_callable($callback)) {
            throw new \InvalidArgumentException(
                'Expect parameter 1 to be an instance of \Closure, or php callable, got : ' . \gettype($callback)
            );
        }

        return new static(
            drewlabs_core_iter_map(
                $this->getIterator(),
                $callback,
                $preserveKey
            )
        );
    }

    /**
     * Skip items in the collection until the given condition is met.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function skipUntil($value)
    {
        $callback = drewlabs_core_is_closure($value) ? $value : (static function ($item) use ($value) {
            return $item === $value;
        });

        return $this->skipWhile(static function (...$params) use ($callback) {
            return !$callback(...$params);
        });
    }

    /**
     * Skip items in the collection while the given condition is met.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function skipWhile($value)
    {
        $callback = drewlabs_core_is_closure($value) ? $value : (static function ($item) use ($value) {
            return $item === $value;
        });

        return new static(function () use ($callback) {
            $iterator = $this->getIterator();
            while ($iterator->valid() && $callback($iterator->current(), $iterator->key())) {
                $iterator->next();
            }
            while ($iterator->valid()) {
                yield $iterator->key() => $iterator->current();
                $iterator->next();
            }
        });
    }

    /**
     * Make an iterator from the given source.
     *
     * @param mixed $source
     *
     * @return \Iterator
     */
    private function makeIterator($source)
    {
        if ($source instanceof \IteratorAggregate) {
            return $source->getIterator();
        }
        if (\is_array($source)) {
            return new \ArrayIterator($source);
        }
        if ($source instanceof \Iterator) {
            return $this->source;
        }

        return $source();
    }
}
