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

use Drewlabs\Contracts\Support\Actions\ActionResult as ActionsActionResult;
use Drewlabs\Support\Immutable\ValueObject;

/**
 * Provide an implementation to the {@link ActionResult} interface
 * that will be easilly serializable to the value it wrapp.
 *
 * */
class ActionResult extends ValueObject implements ActionsActionResult
{
    /**
     * Class instances initializer. It takes as parameter the value to wrap.
     *
     * @param mixed $data
     *
     * @return self
     */
    public function __construct($data)
    {
        parent::__construct(['value' => $data]);
    }

    public function value()
    {
        return $this->value_;
    }

    public function toArray()
    {
        return [
            'value' => $this->value_,
        ];
    }

    public function jsonSerialize()
    {
        return $this->value_;
    }

    protected function getJsonableAttributes()
    {
        return [
            'value_' => 'value',
        ];
    }
}
