<?php

use Drewlabs\Support\Collections\ChunkedStream;
use Drewlabs\Support\Collections\Collectors\ChunkCollector;
use Drewlabs\Support\Collections\Stream;
use Drewlabs\Support\Tests\TestCase;
use Drewlabs\Support\Collections\Contracts\Arrayable;

class ChunkedStreamTest extends TestCase
{
    public function test_chunk_stream_collector()
    {
        $stream = Stream::range(1, 10);
        $this->assertInstanceOf(
            ChunkedStream::class,
            $stream->collect(new ChunkCollector(90)),
        );
    }

    public function test_chunk_stream_map()
    {
        /**
         * @var ChunkedStream
         */
        $stream = Stream::range(1, 10)->collect(new ChunkCollector(2));
        $stream = $stream->map(
            function ($current) {
                return $current * 2;
            }
        );
        $array = $stream->toArray();
        $this->assertEquals(
            $array[0],
            [2, 4]
        );
    }

    public function test_chunk_stream_filter()
    {
        /**
         * @var ChunkedStream
         */
        $stream = Stream::range(1, 10)->collect(new ChunkCollector(3));
        $stream = $stream->filter(
            function ($current) {
                return $current % 2 === 0;
            }
        );
        $array = $stream->toArray();
        $this->assertEquals(
            $array,
            [[2], [4, 6], [8], [10]]
        );
    }

    public function test_chunk_stream_reduce()
    {
        /**
         * @var ChunkedStream
         */
        $stream = Stream::range(1, 10)->collect(new ChunkCollector(3));
        $result = $stream->filter(
            function ($current) {
                return $current % 2 === 0;
            }
        )->reduce(function ($carry, $current) {
            $carry += $current;
            return $carry;
        });
        $this->assertEquals(30, $result);
    }

    public function test_chunk_stream_take()
    {
        /**
         * @var ChunkedStream
         */
        $stream = Stream::range(1, 10)->collect(new ChunkCollector(3));
        $result = $stream->filter(function ($current) {
            return $current % 2 === 0;
        })->take(3)
            ->reduce(0, function ($carry, $current) {
                $carry += $current;
                return $carry;
            });
        $this->assertEquals(20, $result);
    }

    public function test_chunk_stream_first()
    {
        /**
         * @var ChunkedStream
         */
        $stream = Stream::range(1, 10)->collect(new ChunkCollector(3));
        /**
         * @var Arrayable
         */
        $result = $stream->filter(function ($current) {
            return $current % 2 === 0;
        })->first();
        $this->assertInstanceOf(Arrayable::class, $result);
        $this->assertEquals([2], $result->toArray());
    }
}
