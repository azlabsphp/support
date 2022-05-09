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

use Drewlabs\Contracts\Support\Actions\Action as ActionsAction;
use Drewlabs\Contracts\Support\ArrayableInterface;

class Action implements ActionsAction, \JsonSerializable, ArrayableInterface
{
    /**
     * @var string
     */
    private $type_;

    /**
     * @var mixed
     */
    private $payload_;

    public function __construct(array $attributes = [])
    {
        $this->type_ = $attributes['type'] ?? null;
        $this->payload_ = $attributes['payload'] ?? null;
    }

    public function create(string $type, $payload)
    {
        return new self([
            'type' => $type,
            'payload' => $payload,
        ]);
    }

    public function type()
    {
        return $this->type_;
    }

    public function payload()
    {
        return $this->payload_;
    }

    public function toArray()
    {
        return [
            'type' => $this->type_,
            'payload' => $this->payload_,
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
