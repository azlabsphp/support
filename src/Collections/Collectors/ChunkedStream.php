<?php

namespace Drewlabs\Support\Collections\Collectors;

use Drewlabs\Support\Collections\Contracts\CollectorInterface;
use Drewlabs\Support\Collections\ChunkedStream as Chunk;
use Drewlabs\Support\Collections\Stream;
use LogicException;
use Traversable;

/** @package Drewlabs\Support\Collections\Collectors */
class ChunkedStream implements CollectorInterface
{

    const SIZE_LIMIT = 1024;

    /**
     * 
     * @var int
     */
    private $size;

    public function __construct($size = 1024)
    {
        $this->size = $size ? intval($size) : static::SIZE_LIMIT;
    }

    public function __invoke(Traversable $source)
    {

        if ($this->size > static::SIZE_LIMIT) {
            throw new LogicException('For performance reason, chunk size has been limit to 1024');
        }
        return new Chunk((function ($source) {
            $index = 0;
            $array = [];
            foreach ($source as $current) {
                if ($index === $this->size) {
                    yield $this->createStream($array);
                    $index = 0;
                    $array = [];
                }
                $array[] = $current;
                $index++;
            }
            if (!empty($array)) {
                yield $this->createStream($array);
            }
        })($source));
    }

    private function createStream($array)
    {
        return Stream::of((function () use ($array) {
            foreach ($array as $value) {
                yield $value;
            }
        })());
    }
}
