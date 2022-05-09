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

use Drewlabs\Support\Collections\ChunkedStream;
use Drewlabs\Support\Collections\Collectors\ChunkCollector;
use Drewlabs\Support\Collections\Contracts\Arrayable;
use Drewlabs\Support\Collections\Stream;
use Drewlabs\Support\Tests\TestCase;

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
            static function ($current) {
                return $current * 2;
            }
        );
        $array = $stream->toArray();
        $this->assertSame(
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
            static function ($current) {
                return 0 === $current % 2;
            }
        );
        $array = $stream->toArray();
        $this->assertSame(
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
            static function ($current) {
                return 0 === $current % 2;
            }
        )->reduce(static function ($carry, $current) {
            $carry += $current;

            return $carry;
        });
        $this->assertSame(30, $result);
    }

    public function test_chunk_stream_take()
    {
        /**
         * @var ChunkedStream
         */
        $stream = Stream::range(1, 10)->collect(new ChunkCollector(3));
        $result = $stream->filter(static function ($current) {
            return 0 === $current % 2;
        })->take(3)
            ->reduce(0, static function ($carry, $current) {
                $carry += $current;

                return $carry;
            });
        $this->assertSame(20, $result);
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
        $result = $stream->filter(static function ($current) {
            return 0 === $current % 2;
        })->first();
        $this->assertInstanceOf(Arrayable::class, $result);
        $this->assertSame([2], $result->toArray());
    }
}
