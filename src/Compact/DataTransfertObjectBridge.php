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

namespace Drewlabs\Support\Compact;

use Drewlabs\Contracts\Support\DataTransfertObject\ListBridgeInterface;
use Drewlabs\Contracts\Support\DataTransfertObject\ObjectBridgeInterface;
use Drewlabs\Contracts\Support\GenericInterface;
use Drewlabs\Support\Traits\DataTransfertObjectBridge as TraitsDataTransfertObjectBridge;

class DataTransfertObjectBridge implements ObjectBridgeInterface, ListBridgeInterface, GenericInterface
{
    use TraitsDataTransfertObjectBridge;
}
