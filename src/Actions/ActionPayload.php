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

namespace Drewlabs\Support\Actions;

use Drewlabs\Contracts\Support\Actions\ActionPayload as AbstractPayload;

class ActionPayload implements AbstractPayload
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

    /**
     * Based on changes from version 2.4.x, actions wrap it payload values as array
     * even if the value is passed as a single parameter.
     *
     * {@inheritDoc}
     *
     * @return array
     */
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
        return $this->all();
    }
}
