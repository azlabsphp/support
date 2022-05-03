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
use Drewlabs\Support\Collections\LinkedList;

class ChunkCollector implements CollectorInterface
{
    public const SIZE_LIMIT = 512;

    /**
     * @var int
     */
    private $size;

    /**
     * Create a ChunkedStream collector
     * 
     * @param int $size 
     * @return self 
     */
    public function __construct(?int $size = 512)
    {
        $this->size = $size ? (int) $size : static::SIZE_LIMIT;
    }

    public function __invoke(\Traversable $source)
    {
        if ($this->size > static::SIZE_LIMIT) {
            throw new \LogicException('For performance reason, chunk size has been limit to ' . $this->size);
        }

        return new Chunk($this->createChunk($source));
    }

    private function createChunk(\Iterator $source)
    {
        $index = 0;
        $list = new LinkedList;
        foreach ($source as $current) {
            if ($index === $this->size) {
                yield $list->stream();
                $index = 0;
                $list->clear();
            }
            $list->push($current);
            ++$index;
        }
        if (!$list->isEmpty()) {
            yield $list->stream();
            $list->clear();
        }
    }
}
