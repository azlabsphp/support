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
use Drewlabs\Contracts\Support\ArrayableInterface;
use Drewlabs\Support\Traits\MethodProxy;
use JsonSerializable;
use LogicException;

/**
 * Provide an implementation to the {@link ActionResult} interface
 * that will be easilly serializable to the value it wrapp.
 *
 * */
class ActionResult implements ActionsActionResult, JsonSerializable, ArrayableInterface
{
    use MethodProxy;

    /**
     *
     * @var mixed
     */
    private $value_;

    /**
     * Class instances initializer. It takes as parameter the value to wrap.
     *
     * @param mixed $data
     *
     * @return self
     */
    public function __construct($data)
    {
        $this->value_ = $data;
    }

    public function __call($name, $arguments)
    {
        if ($this->value_) {
            return $this->proxy($this->value_, $name, $arguments);
        }
        if ($value = $this->value()) {
            return $this->proxy($value, $name, $arguments);
        }
        throw new \BadMethodCallException("Method $name does not exists on ".__CLASS__);
    }

    public function __get($name)
    {
        if (null !== $this->value_ && is_object($this->value_)) {
            return $this->value_->{$name};
        }
        throw new LogicException("$name does not exists on " . is_object($this->value_) && null !== $this->value_ ? get_class($this->value_) : gettype($this->value_));
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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value_;
    }
}
