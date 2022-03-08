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

use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Support\Collections\Contracts\CollectorInterface;

class ArrayCollector implements CollectorInterface
{
    public function __invoke(\Traversable $source)
    {
        return Arr::create($source);
    }
}
