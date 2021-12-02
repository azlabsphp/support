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

namespace Drewlabs\Support\Immutable;

use Drewlabs\Support\Immutable\Traits\HasModelAttribute;

/**
 * Enhance the default {ValueObject} class with model bindings.
 */
abstract class ModelValueObject extends ValueObject
{
    use HasModelAttribute;

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        $attributes = $this->rebuildAttributesFromModelAttributesIfEmpty()->attributesToArray();

        return empty($attributes) ? null : $attributes;
    }

    protected function rebuildAttributesFromModelAttributesIfEmpty()
    {
        $model = $this->getModel();
        $self = drewlabs_core_is_empty($this->___attributes) && (null !== $model) ?
            (new static($model->attributesToArray()))->withModel($model) : $this;

        return $self;
    }
}
