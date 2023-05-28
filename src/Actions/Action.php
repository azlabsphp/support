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

use Drewlabs\Contracts\Support\Actions\Action as AbstractAction;
use Drewlabs\Contracts\Support\Arrayable;

class Action implements AbstractAction, \JsonSerializable, Arrayable
{
    /**
     * Action type property.
     *
     * @var string
     */
    private $type;

    /**
     * Action payload property.
     *
     * @var mixed
     */
    private $payload;

    /**
     * Creates an {@see Action} class instance.
     *
     * @return self
     */
    public function __construct(array $attributes = [])
    {
        $this->type = $attributes['type'] ?? 'default';
        // Based on changes from v2.4.x, always returns payload as array for
        // API compatibility
        $payload = \is_array($result = ($attributes['payload'] ?? [])) ? $result : [$result];
        $this->payload = new ActionPayload(...$payload);
    }

    /**
     * Creates an {@see Action} instance.
     *
     * @param mixed ...$payload
     *
     * @return static
     */
    public static function create(string $type, ...$payload)
    {
        return new static(['type' => $type, 'payload' => $payload]);
    }

    public function type()
    {
        return $this->type;
    }

    /**
     * **Note**
     * From version 2.4.x, `$action->payload()` calls returns an instance of `ActionPayload`
     * instead of `array` in previous version.
     *
     * {@inheritDoc}
     */
    public function payload()
    {
        return $this->payload;
    }

    public function toArray()
    {
        return ['type' => $this->type(), 'payload' => $this->payload()->toArray()];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
