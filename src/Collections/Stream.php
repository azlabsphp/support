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

use Drewlabs\Contracts\Support\ArrayableInterface;
use Drewlabs\Core\Helpers\Functional;
use Drewlabs\Core\Helpers\Iter;
use Drewlabs\Support\Collections\Collectors\ArrayCollector;
use Drewlabs\Support\Collections\Contracts\Arrayable;
use Drewlabs\Support\Collections\Contracts\StreamInterface;
use Drewlabs\Support\Collections\Traits\BaseStream;

class Stream implements
    \IteratorAggregate,
    StreamInterface,
    ArrayableInterface,
    Arrayable
{
    use BaseStream;

    private function __construct(\Traversable $source, bool $infinite = false)
    {
        $this->source = $source;
        $this->infinite = $infinite;
    }

    public static function of(\Traversable $source)
    {
        return new self($source);
    }

    /**
     * @param int|mixed $seed
     *
     * @return Stream
     */
    public static function iterate($seed, \Closure $callback)
    {
        $source = (static function () use (&$seed, $callback) {
            yield $seed;
            while (true) {
                $seed = $callback($seed);
                yield $seed;
            }
        })();

        return new self($source, true);
    }

    /**
     * Create a stream from a range of values.
     *
     * @param int $steps
     *
     * @throws \LogicException
     *
     * @return Stream
     */
    public static function range(int $start, int $end, $steps = 1)
    {
        return new self(Iter::range($start, $end, $steps));
    }

    public function reduce($identityOrFunc, callable $callback = null)
    {
        $this->_throwIfUnsafe();
        [$identity, $callback] = func_num_args() === 1 ?
            [0, $identityOrFunc] :
            [$identityOrFunc, $callback];
        $result = $identity;
        $this->pipe[] = static function ($current) use ($callback, &$result) {
            if ($current->accepts()) {
                $result = $callback($result, $current->value);
            }
            return $result;
        };
        $composedFunc = Functional::compose(...$this->pipe);
        foreach ($this->source as $value) {
            $result = $composedFunc(StreamInput::wrap($value));
        }

        return $result;
    }

    public function filter(callable $callback)
    {
        $this->pipe[] = function ($source) use ($callback) {
            return Operator::create()(
                StreamInput::wrap(
                    $source->value,
                    $callback($source->value)
                )
            );
        };
        return $this;
    }

    public function map(callable $callback)
    {
        $this->pipe[] = Operator::create($callback);

        return $this;
    }

    public function toArray()
    {
        return $this->collect(new ArrayCollector());
    }
}
