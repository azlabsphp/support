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

use Drewlabs\Support\XML\Contracts\XMLElementInterface;

class XMLElement implements XMLElementInterface
{
    /**
     * @var string
     */
    private $name_;

    /**
     * @var string
     */
    private $namespace_;

    /**
     * @var string|Node[]
     */
    private $value_;

    /**
     * @var XMLAttribute[]
     */
    private $attributes_;

    public function __construct(
        string $name,
        $value = '',
        string $ns = '',
        $attributes = [],
        ?string $xmlns = null
    ) {
        if (!\is_array($attributes) && !($attributes instanceof XMLAttribute) && !(null === $attributes)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Attribute property must be of type array or a %s',
                    XMLAttribute::class
                )
            );
        }
        $this->name_ = $name;
        $this->namespace_ = $ns ?? '';
        $this->value_ = $value ?? '';
        $this->attributes_ = $attributes instanceof XMLAttribute ? [$attributes] : ($attributes ?? []);
        if ($xmlns) {
            $this->attributes_[] = new XMLAttribute('xmlns', $xmlns);
        }
    }

    public function __set($name, $value)
    {
        throw new \Exception(__CLASS__.' properties are not mutable.');
    }

    public function __get($name)
    {
        if ('value_' === $name) {
            return $this->{$name} ? ($this->{$name} instanceof self ? [$this->{$name}] : $this->{$name}) : '';
        }
        if ('attributes_' === $name) {
            return $this->{$name} ? ($this->{$name} instanceof XMLAttribute ? [$this->{$name}] : $this->{$name}) : null;
        }

        return $this->{$name};
    }

    public function attributes()
    {
        return $this->__get('attributes_');
    }

    public function name(): string
    {
        return $this->__get('name_');
    }

    public function namespace()
    {
        return $this->__get('namespace_');
    }

    public function value()
    {
        return $this->__get('value_');
    }
}
