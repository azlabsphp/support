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

namespace Drewlabs\Support\DI\Exceptions;

class TypeResolutionException extends \Exception
{
    public function __construct($concrete, ?string $abstract = null)
    {
        $name = \is_string($concrete) ? $concrete : __CLASS__;
        $message = null === $abstract ? "Target [$name] is not instantiable." : "Target [$abstract] is not instantiable while building [$name].";
        parent::__construct($message);
    }
}
