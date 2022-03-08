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

namespace Drewlabs\Support\Collections\Collectors;

use Drewlabs\Support\Collections\ChunkedStream as Chunk;
use Drewlabs\Support\Collections\Contracts\CollectorInterface;
use Drewlabs\Support\Collections\Stream;

class ChunkedStream implements CollectorInterface
{
    public const SIZE_LIMIT = 1024;

    /**
     * @var int
     */
    private $size;

    public function __construct($size = 1024)
    {
        $this->size = $size ? (int) $size : static::SIZE_LIMIT;
    }

    public function __invoke(\Traversable $source)
    {
        if ($this->size > static::SIZE_LIMIT) {
            throw new \LogicException('For performance reason, chunk size has been limit to 1024');
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
                ++$index;
            }
            if (!empty($array)) {
                yield $this->createStream($array);
            }
        })($source));
    }

    private function createStream($array)
    {
        return Stream::of((static function () use ($array) {
            foreach ($array as $value) {
                yield $value;
            }
        })());
    }
}
