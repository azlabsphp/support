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

use Drewlabs\Contracts\Support\Actions\ActionResult as AbstractActionResult;
use Drewlabs\Support\Traits\MethodProxy;

/**
 * Provide an implementation to the {@link ActionResult} interface
 * that will be easilly serializable to the value it wrapp.
 *
 * */
class ActionResult implements AbstractActionResult, \JsonSerializable
{
    use MethodProxy;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Class instances initializer. It takes as parameter the value to wrap.
     *
     * @param mixed $data
     *
     * @return self
     */
    public function __construct($data)
    {
        $this->value = $data;
    }

    /**
     * PHP magic method indirecting calls on the current object to the value it wraps.
     *
     * @param mixed $name
     * @param mixed $arguments
     *
     * @throws \Error
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($this->value) {
            return $this->proxy($this->value, $name, $arguments);
        }
        if ($value = $this->value()) {
            return $this->proxy($value, $name, $arguments);
        }
        throw new \BadMethodCallException("Method $name does not exists on ".__CLASS__);
    }

    /**
     * PHP magic method redirecting property getter call to wrapped value.
     *
     * @param mixed $name
     *
     * @throws \LogicException
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (null !== $this->value && \is_object($this->value)) {
            return $this->value->{$name};
        }
        throw new \LogicException("$name does not exists on ".\is_object($this->value) && null !== $this->value ? \get_class($this->value) : \gettype($this->value));
    }

    public function value()
    {
        return $this->value;
    }

    public function hasValue(): bool
    {
        return null !== $this->value;
    }

    public function toArray()
    {
        return ['value' => $this->value];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->value;
    }
}
