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

use Closure;
use Drewlabs\Contracts\Support\ArrayableInterface;
use Drewlabs\Core\Helpers\Functional;
use Drewlabs\Core\Helpers\Iter;
use Drewlabs\Support\Collections\Collectors\ArrayCollector;

use Drewlabs\Support\Collections\Contracts\StreamInterface;

class Stream implements \IteratorAggregate, StreamInterface, ArrayableInterface
{
    /**
     * @var array<Closure<>>
     */
    private $pipe = [];

    /**
     * @var \Iterator
     */
    private $source;

    /**
     * @var bool
     */
    private $infinite;

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

    public function map(callable $callback)
    {
        $this->pipe[] = $this->createOperator($callback);

        return $this;
    }

    public function reduce($identity, callable $callback)
    {
        $this->_throwIfUnsafe();
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
            return $this->createOperator()(StreamInput::wrap($source->value, $callback($source->value)));
        };

        return $this;
    }

    public function firstOr($default = null)
    {
        $composedFunc = Functional::compose(...$this->pipe);
        foreach ($this->source as $value) {
            $result = $composedFunc(StreamInput::wrap($value));
            if ($result->accepts()) {
                return $result->value;
            }
        }

        return Functional::isCallable($default) ? \call_user_func($default) : $default;
    }

    public function first()
    {
        return $this->firstOr(null);
    }

    public function take(int $n)
    {
        $this->infinite = false;
        $this->source = (static function ($source) use ($n) {
            $index = 0;
            foreach ($source as $current) {
                ++$index;
                if ($index > $n) {
                    break;
                }
                yield $current;
            }
        })($this->source);

        return $this;
    }

    public function takeUntil($value)
    {
        $this->infinite = false;
        $value = $this->isCallable($value) ? $value : static function ($current) use ($value) {
            return $current === $value;
        };
        $this->source = (static function ($source) use ($value) {
            while ($source->valid()) {
                $current = $source->current();
                if ($value($current, $source->key())) {
                    break;
                }
                yield $current;
                $source->next();
            }
        })($this->source);

        return $this;
    }

    public function takeWhile($value, $flexible = true)
    {
        $value = $this->isCallable($value) ? $value : static function ($data) use ($value) {
            return $data === $value;
        };
        $this->source = (static function ($source) use ($value, $flexible) {
            while ($source->valid()) {
                $current = $source->current();
                if ($result = $value($current, $source->key())) {
                    yield $current;
                }
                if (!(bool) $flexible && !(bool) $result) {
                    break;
                }
                $source->next();
            }
        })($this->source);

        return $this;
    }

    /**
     * Set an offset on the number of stream data.
     *
     * @param mixed $n
     *
     * @return $this
     */
    public function skip(int $n)
    {
        $this->source = (static function ($source) use ($n) {
            $index = 0;
            foreach ($source as $current) {
                ++$index;
                if ($index <= $n) {
                    continue;
                }
                yield $current;
            }
        })($this->source);

        return $this;
    }

    public function each(callable $callback)
    {
        $this->_throwIfUnsafe();
        $this->pipe[] = $this->createOperator($callback);
        $composedFunc = Functional::compose(...$this->pipe);
        foreach ($this->source as $value) {
            $composedFunc(StreamInput::wrap($value));
        }
    }

    public function collect(callable $collector)
    {
        $this->_throwIfUnsafe();
        $composedFunc = Functional::compose(...$this->pipe);

        return \call_user_func(
            $collector,
            (static function ($source) use (&$composedFunc) {
                foreach ($source as $value) {
                    $result = $composedFunc(StreamInput::wrap($value));
                    if (!$result->accepts()) {
                        continue;
                    }
                    yield $result->value;
                }
            })($this->source)
        );
    }

    public function toArray()
    {
        return $this->collect(new ArrayCollector());
    }

    public function getIterator(): \Traversable
    {
        return $this->source;
    }

    private function createOperator($callback = null)
    {
        return new class($callback) {
            private $closure;

            public function __construct($closure = null)
            {
                $this->closure = $closure;
            }

            public function __invoke($data)
            {
                if ($accepts = (bool) ($data->accepts())) {
                    return null === $this->closure ?
                        $data :
                        StreamInput::wrap(
                            \call_user_func($this->closure, $data->value),
                            $accepts
                        );
                }

                return $data;
            }
        };
    }

    private function isCallable($value)
    {
        return !\is_string($value) && \is_callable($value);
    }

    private function _throwIfUnsafe()
    {
        if ($this->infinite) {
            throw new \Exception(
                'Stream source is unsafe, stream is infinite call take(n) to process finite number of source items'
            );
        }
    }
}
