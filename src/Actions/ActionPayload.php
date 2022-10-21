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

namespace Drewlabs\Support\Actions;

use Drewlabs\Contracts\Support\Actions\ActionPayload as PayloadInterface;

/** @package Drewlabs\Support\Actions */
class ActionPayload implements PayloadInterface
{
    /**
     * Payload values property.
     *
     * @var array
     */
    private $values;

    /**
     * Creates an {@see ActionPayload} instance. Constructor parameter is variadic in order to always create
     * values properties as array no matter the parameter. It makes the payload to be be easily decomposable
     * when passed to a function.
     *
     * @param mixed $values
     *
     * @return static
     */
    public function __construct(...$values)
    {
        $this->values = $values;
    }

    public function value()
    {
        return $this->values;
    }

    public function all()
    {
        return $this->values;
    }

    public function toArray()
    {
        return $this->values;
    }
}
