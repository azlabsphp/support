<?php

namespace Drewlabs\Support\Collections;

use Drewlabs\Core\Helpers\Arr;
use Traversable;

class StreamCollectors
{

    public static function toArray(Traversable $source)
    {
        return Arr::create($source);
    }
}
