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

namespace Drewlabs\Support\Exceptions;

class NullPointerException extends \Exception
{
    public function __construct(string $property)
    {
        parent::__construct("ERROR : $property value is not initialized");
    }
}