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
        return Arr::create((static function ($source) {
            /**
             * @var Stream $value
             */
            foreach ($source as $value) {
                yield $value->toArray();
            }
        })($this->source));
    }
}
