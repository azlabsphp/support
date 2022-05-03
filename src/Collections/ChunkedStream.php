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

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Functional;
use Drewlabs\Support\Collections\Contracts\Arrayable;
use Drewlabs\Support\Collections\Contracts\StreamInterface;
use Drewlabs\Support\Collections\Traits\BaseStream;

/** @package Drewlabs\Support\Collections */
class ChunkedStream implements StreamInterface, Arrayable
{
    use BaseStream;

    public function __construct(\Traversable $source)
    {
        $this->source = $source;
    }

    public function map(callable $callback)
    {
        $this->pipe[] = function (StreamInput $input) use ($callback) {
            $stream = $input->value->map($callback);
            return Operator::create()(StreamInput::wrap($stream));
        };
        return $this;
    }

    public function filter(callable $predicate)
    {
        $this->pipe[] = function (StreamInput $input) use ($predicate) {
            return Operator::create()(StreamInput::wrap($input->value->filter($predicate)));
        };
        return $this;
    }

    public function reduce($identityOrFunc, callable $reducer = null)
    {
        [$identity, $reducer] = func_num_args() === 1 ?
            [0, $identityOrFunc] :
            [$identityOrFunc, $reducer];
        $result = $identity;
        // For chunk stream reducer function we strive to reduce
        // value of each chunk recursively
        $this->pipe[] = static function (StreamInput $current) use ($reducer, &$result) {
            if ($current->accepts() && ($current->value)) {
                $result = $current->value->reduce($result, $reducer);
            }
            return $result;
        };
        $composedFunc = Functional::compose(...$this->pipe);
        foreach ($this->source as $value) {
            $result = $composedFunc(StreamInput::wrap($value));
        }

        return $result;
    }

    public function toArray()
    {
        $fn = static function ($source) {
            /**
             * @var \Drewlabs\Support\Collections\Stream $value
             */
            foreach ($source as $value) {
                yield $value->toArray();
            }
        };
        return Arr::create(
            $this->collect(function ($source) use (&$fn) {
                return $fn($source);
            })
        );
    }
}
