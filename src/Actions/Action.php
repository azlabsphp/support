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
use Drewlabs\Support\Immutable\ValueObject;

class Action extends ValueObject implements ActionsAction
{
    public function type()
    {
        return $this->type_;
    }

    public function payload()
    {
        return $this->payload_;
    }

    protected function getJsonableAttributes()
    {
        return [
            'type_' => 'type',
            'payload_' => 'payload',
        ];
    }
}
