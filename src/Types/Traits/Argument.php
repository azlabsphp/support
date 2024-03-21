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

namespace Drewlabs\Support\Types\Traits;

use Drewlabs\Support\Types\ArgumentType;

/**
 * @deprecated
 */
trait Argument
{
    public function isOptional(): bool
    {
        return ArgumentType::OPTIONAL === $this->state;
    }

    /**
     * Returns the argument type binded to the current Function argument.
     *
     * @return string|mixed
     */
    public function getType()
    {
        return $this->type;
    }
}
