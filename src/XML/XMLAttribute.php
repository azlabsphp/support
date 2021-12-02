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

namespace Drewlabs\Support\XML;

use Drewlabs\Support\XML\Contracts\XMLAttributeInterface;

class XMLAttribute implements XMLAttributeInterface
{
    /**
     * @var string
     */
    private $name_;

    /**
     * @var string
     */
    private $value_;

    public function __construct(string $name, $value = '')
    {
        $this->name_ = $name;
        $this->value_ = (string) ($value ?? '');
    }

    public function __set($name, $value)
    {
        throw new \Exception(__CLASS__.' properties are not mutable.');
    }

    public function name(): string
    {
        return $this->name_;
    }

    public function value(): string
    {
        return $this->value_;
    }
}
