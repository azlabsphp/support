<?php

namespace Drewlabs\Support\Collections\Traits;

use CachingIterator;
use Closure;
use Drewlabs\Support\Collections\SimpleCollection;
use Exception;
use JsonSerializable;

// Should review : strictContains(), eachSpread(), mapSpread(), slice(), collapse()

trait Enumerable
{

    /**
     * The methods that can be proxied.
     *
     * @var string[]
     */
    protected static $proxies = [
        'average',
        'avg',
        'contains',
        'each',
        'every',
        'filter',
        'first',
        'flatMap',
        'groupBy',
        'keyBy',
        'map',
        'max',
        'min',
        'partition',
        'reject',
        'skipUntil',
        'skipWhile',
        'some',
        'sortBy',
        'sortByDesc',
        'sum',
        'takeUntil',
        'takeWhile',
        'unique',
        'until',
    ];

    /**
     * Create a new instance with no items.
     *
     * @return static
     */
    public static function empty()
    {
        return new self([]);
    }

    /**
     * Alias for the "avg" method.
     *
     * @param  callable|string|null  $callback
     * @return mixed
     */
    public function average($callback = null)
    {
        return $this->avg($callback);
    }

    /**
     * Get the average value of a given key.
     *
     * @param  callable|string|null  $callback
     * @return mixed
     */
    public function avg($callback = null)
    {
        $callback = drewlabs_core_create_get_callback($callback);
        $items = $this->map(
            function ($value) use ($callback) {
                return $callback($value);
            }
        )->filter(
            function ($value) {
                return null !== $value;
            }
        );

        if ($count = $items->count()) {
            return $items->sum() / $count;
        }
    }

    /**
     * Get the sum of the given values.
     *
     * @param  callable|string|null  $callback
     * @return mixed
     */
    public function sum($callback = null)
    {
        $callback = null === $callback
            ? function ($value) {
                return $value;
            }
            : drewlabs_core_create_get_callback($callback);

        return $this->reduce(function ($result, $item) use ($callback) {
            return $result + $callback($item);
        }, 0);
    }

    /**
     * Determine if an item exists, using strict comparison.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return bool
     */
    public function containsStrict($key, $value = null)
    {
        if (func_num_args() === 2) {
            return $this->contains(function ($item) use ($key, $value) {
                return drewlabs_core_get($item, $key) === $value;
            });
        }

        if (drewlabs_core_is_closure($key)) {
            return null !== $this->first($key);
        }

        foreach ($this as $item) {
            if ($item === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Execute a callback over each nested chunk of items.
     *
     * @param  callable  $callback
     * @return void
     */
    public function eachSpread(callable $callback)
    {
        return $this->each(function ($chunk, $key) use ($callback) {
            $chunk[] = $key;
            return $callback(...$chunk);
        });
    }



    /**
     * Determine if all items pass the given truth test.
     *
     * @param  string|callable  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function every($key, $operator = null, $value = null)
    {
        if (func_num_args() === 1) {
            $callback = drewlabs_core_create_get_callback($key);
            foreach ($this as $k => $v) {
                if (!$callback($v, $k)) {
                    return false;
                }
            }
            return true;
        }

        return $this->every(drewlabs_core_create_evaluation_callback(...func_get_args()));
    }

    /**
     * Get the first item by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return mixed
     */
    public function firstWhere($key, $operator = null, $value = null)
    {
        return $this->first(drewlabs_core_create_evaluation_callback(...func_get_args()));
    }

    /**
     * Determine if the collection is not empty.
     *
     * @return bool
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    /**
     * Run a map over each nested chunk of items.
     *
     * @param  callable  $callback
     * @param bool $preserve_key 
     * @return static
     */
    public function mapSpread(callable $callback, bool $preserve_key = true)
    {
        return $this->map(function ($chunk, $key) use ($callback) {
            $chunk = drewlabs_core_array_wrap($chunk);
            $chunk[] = $key;
            return $callback(...$chunk);
        }, $preserve_key);
    }

    /**
     * Map a collection and flatten the result by a single level.
     *
     * @param  callable|\Closure|mixed  $callback
     * @return static
     */
    public function flatMap($callback)
    {
        return $this->map($callback)->collapse();
    }

    /**
     * Map the values into a new class.
     *
     * @param  string  $class
     * @return static
     */
    public function mapInto($class)
    {
        return $this->map(function ($value, $key) use ($class) {
            return new $class($value, $key);
        });
    }

    /**
     * Get the min value of a given key.
     *
     * @param  callable|string|null  $callback
     * @return mixed
     */
    public function min($callback = null)
    {
        $callback = drewlabs_core_create_get_callback($callback);
        return $this->map(
            function ($value) use ($callback) {
                return $callback($value);
            }
        )->filter(
            function ($value) {
                return null !== $value;
            }
        )->reduce(
            function ($result, $value) {
                return (null === $result) || ($value < $result) ? $value : $result;
            }
        );
    }

    /**
     * Get the max value of a given key.
     *
     * @param  callable|string|null  $callback
     * @return mixed
     */
    public function max($callback = null)
    {
        $callback = drewlabs_core_create_get_callback($callback);

        return $this->filter(
            function ($value) {
                return null !== $value;
            }
        )->reduce(
            function ($result, $item) use ($callback) {
                $value = $callback($item);
                return (null === $result) || ($value > $result) ? $value : $result;
            }
        );
    }

    /**
     * "Paginate" the collection by slicing it into a smaller collection.
     *
     * @param  int  $page
     * @param  int  $perPage
     * @return static
     */
    public function forPage(int $page, int $perPage = 20, ?bool $preserve_key = true)
    {
        $offset = max(0, ($page - 1) * $perPage);
        return $this->slice($offset, $perPage, $preserve_key);
    }


    /**
     * Apply the callback if the value is truthy.
     *
     * @param  bool|mixed  $value
     * @param  callable|null  $callback
     * @param  callable|null  $default
     * @return static|mixed
     */
    public function when($value, callable $callback = null, callable $default = null)
    {
        if (null === $callback) {
            return drewlabs_core_create_when_proxy_callback($this, $value);
        }
        if ($value) {
            return $callback($this, $value);
        } elseif ($default) {
            return $default($this, $value);
        }
        return $this;
    }

    /**
     * Apply the callback if the collection is empty.
     *
     * @param  callable|null  $callback
     * @param  callable|null  $default
     * @return static|mixed
     */
    public function whenEmpty(?callable $callback = null, callable $default = null)
    {
        return $this->when($this->isEmpty(), $callback, $default);
    }

    /**
     * Apply the callback if the collection is not empty.
     *
     * @param  callable|null  $callback
     * @param  callable|null  $default
     * @return static|mixed
     */
    public function whenNotEmpty(callable $callback = null, callable $default = null)
    {
        return $this->when($this->isNotEmpty(), $callback, $default);
    }

    /**
     * Apply the callback if the value is falsy.
     *
     * @param  bool  $value
     * @param  callable|null  $callback
     * @param  callable|null  $default
     * @return static|mixed
     */
    public function unless($value, ?callable $callback = null, callable $default = null)
    {
        return $this->when(!$value, $callback, $default);
    }

    /**
     * Apply the callback unless the collection is empty.
     *
     * @param  callable|null  $callback
     * @param  callable|null  $default
     * @return static|mixed
     */
    public function unlessEmpty(?callable $callback = null, callable $default = null)
    {
        return $this->whenNotEmpty($callback, $default);
    }

    /**
     * Apply the callback unless the collection is not empty.
     *
     * @param  callable|null  $callback
     * @param  callable|null  $default
     * @return static|mixed
     */
    public function unlessNotEmpty(?callable $callback = null, callable $default = null)
    {
        return $this->whenEmpty($callback, $default);
    }



    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return static
     */
    public function where($key, $operator = null, $value = null)
    {
        return $this->filter(drewlabs_core_create_evaluation_callback(...func_get_args()));
    }

    /**
     * Filter items where the value for the given key is null.
     *
     * @param  string|null  $key
     * @return static
     */
    public function whereNull($key = null)
    {
        return $this->whereStrict($key, null);
    }

    /**
     * Filter items where the value for the given key is not null.
     *
     * @param  string|null  $key
     * @return static
     */
    public function whereNotNull($key = null)
    {
        return $this->where($key, '!==', null);
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return static
     */
    public function whereStrict($key, $value)
    {
        return $this->where($key, '===', $value);
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $values
     * @param  bool  $strict
     * @return static
     */
    public function whereIn($key, $values, $strict = false)
    {
        $values = drewlabs_core_array_udt_to_array($values);
        return $this->filter(
            function ($item) use ($key, $values, $strict) {
                return in_array(drewlabs_core_get($item, $key), $values, $strict);
            }
        );
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param  string  $key
     * @param  mixed  $values
     * @return static
     */
    public function whereInStrict($key, $values)
    {
        return $this->whereIn($key, $values, true);
    }

    /**
     * Filter items such that the value of the given key is between the given values.
     *
     * @param  string  $key
     * @param  array  $values
     * @return static
     */
    public function whereBetween($key, $values)
    {
        return $this->where($key, '>=', reset($values))
            ->where($key, '<=', end($values));
    }

    /**
     * Filter items such that the value of the given key is not between the given values.
     *
     * @param  string  $key
     * @param  array  $values
     * @return static
     */
    public function whereNotBetween($key, $values)
    {
        return $this->filter(
            function ($item) use ($key, $values) {
                return drewlabs_core_get($item, $key) < reset($values) || drewlabs_core_get($item, $key) > end($values);
            }
        );
    }

    /**
     * Filter items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $values
     * @param  bool  $strict
     * @return static
     */
    public function whereNotIn($key, $values, $strict = false)
    {
        $values = drewlabs_core_array_udt_to_array($values);
        return $this->reject(
            function ($item) use ($key, $values, $strict) {
                return in_array(drewlabs_core_get($item, $key), $values, $strict);
            }
        );
    }

    /**
     * Filter items by the given key value pair using strict comparison.
     *
     * @param  string  $key
     * @param  mixed  $values
     * @return static
     */
    public function whereNotInStrict($key, $values)
    {
        return $this->whereNotIn($key, $values, true);
    }

    /**
     * Filter the items, removing any items that don't match the given type(s).
     *
     * @param  string|string[]  $type
     * @return static
     */
    public function whereInstanceOf(...$types)
    {
        return $this->filter(function ($value) use ($types) {
            foreach ($types as $classType) {
                if ($value instanceof $classType) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Pass the collection to the given callback and return the result.
     *
     * @param  callable[]|\Closure[]  $callback
     * @return mixed
     */
    public function pipe(...$callback)
    {
        return drewlabs_core_fn_compose(...$callback)($this);
    }

    /**
     * Dynamically access collection proxies.
     *
     * @param  string  $proxy
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($proxy)
    {
        if (!in_array($proxy, static::$proxies)) {
            throw new Exception("Property [{$proxy}] does not exist on this collection instance.");
        }
        return drewlabs_core_high_order_proxy_callback($this, $proxy);
    }

    /**
     * Pass the collection into a new class.
     *
     * @param  string  $class
     * @return mixed
     */
    public function pipeInto(string $class)
    {
        return new $class($this);
    }

    /**
     * Pass the collection to the given callback and then return it.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function tap(callable $callback)
    {
        // Clone the collection in order to not modify it
        $callback(new static($this));
        return $this;
    }

    /**
     * Create a collection of all elements that do not pass a given truth test.
     *
     * @param  callable|mixed  $callback
     * @return static
     */
    public function reject($callback = true)
    {
        $as_callback = drewlabs_core_is_closure($callback);
        return $this->filter(
            function ($value, $key) use ($callback, $as_callback) {
                return $as_callback
                    ? !$callback($value, $key)
                    : $value !== $callback;
            }
        );
    }

    /**
     * Return only unique items from the collection array using strict comparison.
     *
     * @param  string|callable|null  $key
     * @return static
     */
    public function uniqueStrict($key = null)
    {
        return $this->unique($key, true);
    }

    /**
     * Collect the values into a collection.
     *
     * @return SimpleCollection
     */
    public function collect()
    {
        return new SimpleCollection($this->all());
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_map(
            function ($value) {
                if ($value instanceof JsonSerializable) {
                    return $value->jsonSerialize();
                } elseif (method_exists($value, 'toJson')) {
                    return json_decode($value->toJson(), true);
                } elseif (method_exists($value, 'toArray')) {
                    return $value->toArray();
                }
                return $value;
            },
            $this->all()
        );
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Get a CachingIterator instance.
     *
     * @param  int  $flags
     * @return \CachingIterator
     */
    public function getCachingIterator($flags = CachingIterator::CALL_TOSTRING)
    {
        return new CachingIterator($this->getIterator(), $flags);
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Add a method to the list of proxied methods.
     *
     * @param  string  $method
     * @return void
     */
    public static function proxy($method)
    {
        static::$proxies[] = $method;
    }
}
