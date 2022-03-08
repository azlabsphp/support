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

namespace Drewlabs\Support\Types;

class Nullable
{
    /**
     * @var mixed|null
     */
    private $value_;

    /**
     * @var mixed|null
     */
    private $default_;

    public function __construct($value, $default = null)
    {
        $this->value_ = $value;
        $this->default_ = $default;
    }

    public function value()
    {
        return $this->value_ ?? $this->resolveClosure();
    }

    public function hasValue()
    {
        $tmp = $this->value_ ?? $this->resolveClosure();

        return null !== $tmp;
    }

    public function isNull()
    {
        return !$this->hasValue();
    }

    private function resolveClosure()
    {
        return \is_callable($this->default_) && !\is_string($this->default_) ?
            \call_user_func($this->default_) :
            $this->default_;
    }
}
