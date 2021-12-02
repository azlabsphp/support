<?php

namespace Drewlabs\Support\Collections\Traits;

trait Sortable
{
    /**
     * Sort through each item with a callback.
     *
     * @param  callable|int|null  $callback
     * @return static
     */
    public function sort($callback = null)
    {
        /**
         * @var array
         */
        $items = $this->all();

        $callback && is_callable($callback)
            ? uasort($items, $callback)
            : asort($items, $callback ?? SORT_REGULAR);

        return new static($items);
    }

    /**
     * Sort items in descending order.
     *
     * @param  int  $options
     * @return static
     */
    public function sortDesc($options = SORT_REGULAR)
    {
        $items = $this->all();
        arsort($items, $options);
        return new static($items);
    }

    /**
     * Sort the collection using the given callback.
     *
     * @param  callable|array|string  $callback
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        if (is_array($callback) && !is_callable($callback)) {
            return $this->sortByMany($callback);
        }
        $results = [];
        $callback = drewlabs_core_create_get_callback($callback);
        $items = $this->all();
        foreach ($items as $key => $value) {
            $results[$key] = $callback($value, $key);
        }
        $descending ? arsort($results, $options)
            : asort($results, $options);
        foreach (array_keys($results) as $key) {
            $results[$key] = $items[$key];
        }

        return new static($results);
    }

    /**
     * Sort the collection using multiple comparisons.
     *
     * @param  array  $comparisons
     * @return static
     */
    protected function sortByMany(array $comparisons = [])
    {
        $items = $this->all();
        usort($items, function ($a, $b) use ($comparisons) {
            foreach ($comparisons as $comparison) {
                $comparison = drewlabs_core_array_wrap($comparison);
                $prop = $comparison[0];
                $ascending = drewlabs_core_get($comparison, 1, true) === true ||
                    drewlabs_core_get($comparison, 1, true) === 'asc';
                $result = 0;
                if (is_callable($prop)) {
                    $result = $prop($a, $b);
                } else {
                    $values = [
                        drewlabs_core_get($a, $prop),
                        drewlabs_core_get($b, $prop)
                    ];
                    if (!$ascending) {
                        $values = array_reverse($values);
                    }
                    $result = $values[0] <=> $values[1];
                }
                if ($result === 0) {
                    continue;
                }
                return $result;
            }
        });
        return new static($items);
    }

    /**
     * Sort the collection in descending order using the given callback.
     *
     * @param  callable|string  $callback
     * @param  int  $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR)
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * Sort the collection keys.
     *
     * @param  int  $options
     * @param  bool  $descending
     * @return static
     */
    public function sortKeys($options = SORT_REGULAR, $descending = false)
    {
        $items = $this->all();
        $descending ? krsort($items, $options) : ksort($items, $options);
        return new static($items);
    }

    /**
     * Sort the collection keys in descending order.
     *
     * @param  int  $options
     * @return static
     */
    public function sortKeysDesc($options = SORT_REGULAR)
    {
        return $this->sortKeys($options, true);
    }
}
