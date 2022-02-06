<?php

namespace Drewlabs\Support\Collections\Collectors;

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Support\Collections\Contracts\CollectorInterface;

class ArrayCollector implements CollectorInterface
{

    public function __invoke(\Traversable $source)
    {
        return Arr::create($source);
    }
}
