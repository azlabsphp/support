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

namespace Drewlabs\Support\Immutable\Exceptions;

class ImmutableObjectException extends \Exception
{
    public function __construct(string $clazz)
    {
        if (!\is_string($clazz)) {
            throw new \InvalidArgumentException('Constructor parameter must be a string');
        }
        $message = sprintf('%s class is immutable, it does not provides property setters', $clazz);
        parent::__construct($message);
    }
}
