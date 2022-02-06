<?php

namespace Drewlabs\Support\Collections;

use Drewlabs\Core\Helpers\Arr;

class ChunkedStream
{
    // TODO : Implements the StreamInterface
    /**
     * @var \Iterator<int,Stream>
     */
    private $source;

    public function __construct(\Traversable $source)
    {
        $this->source = $source;
    }

    public function toArray()
    {
        return Arr::create((function ($source) {
            /**
             * @var Stream $value
             */
            foreach ($source as $value) {
                yield $value->toArray();
            }
        })($this->source));
    }
}
