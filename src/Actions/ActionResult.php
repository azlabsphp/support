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

use BadMethodCallException;
use Drewlabs\Contracts\Support\Actions\ActionResult as ActionsActionResult;
use Drewlabs\Support\Immutable\ValueObject;
use Drewlabs\Support\Traits\MethodProxy;

/**
 * Provide an implementation to the {@link ActionResult} interface
 * that will be easilly serializable to the value it wrapp.
 *
 * */
class ActionResult extends ValueObject implements ActionsActionResult
{
    use MethodProxy;
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

    public function __call($name, $arguments)
    {
        if ($this->value_) {
            return $this->proxy($this->value_, $name, $arguments);
        }
        if ($value = $this->value()) {
            return $this->proxy($value, $name, $arguments);
        }
        throw new BadMethodCallException("Method $name does not exists on " . __CLASS__);
    }
}
