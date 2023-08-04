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

namespace Drewlabs\Support\Types;

class Nullable
{
    /**
     * @var mixed|null
     */
    private $value;

    /**
     * @var mixed|null
     */
    private $default;

    /**
     * Creates class instance.
     *
     * @param mixed $value
     * @param mixed $default
     */
    public function __construct($value, $default = null)
    {
        $this->value = $value;
        $this->default = $default;
    }

    public function value()
    {
        return $this->value ?? $this->resolveClosure();
    }

    public function hasValue()
    {
        $tmp = $this->value ?? $this->resolveClosure();

        return null !== $tmp;
    }

    public function isNull()
    {
        return !$this->hasValue();
    }

    private function resolveClosure()
    {
        return \is_callable($this->default) && !\is_string($this->default) ?
            \call_user_func($this->default) :
            $this->default;
    }
}
