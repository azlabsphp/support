<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Support\Exceptions;

class InvalidTypeException extends \Exception
{
    /**
     * Creates an {@see InvalidTypeException} instance.
     *
     * @return self
     */
    public function __construct(string $property, string $expected, string $got)
    {
        parent::__construct("Wrong type for $property, Expected $expected, Got: $got");
    }
}
