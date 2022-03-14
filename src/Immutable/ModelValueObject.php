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

use Drewlabs\Contracts\Data\Model\Model;
use Drewlabs\Core\Helpers\Arr;
use Drewlabs\Core\Helpers\Str;
use Drewlabs\Support\Traits\MethodProxy;

/**
 * Enhance the default {ValueObject} class with model bindings.
 */
abstract class ModelValueObject extends ValueObject
{
    use MethodProxy;

    /**
     * @var Model|mixed
     */
    private $___model;

    /**
     * @param \stdObject|Model|array $attributes
     *
     * @return void
     */
    public function __construct($attributes = [])
    {
        $this->initializeAttributes();
        if ($attributes instanceof Model) {
            // TODO : SET MODEL INSTANCE FOR CLASS USER TO MANIPULATE DURING SERIALISATIOM
            $this->setModel($attributes);
            $this->setHidden(
                array_merge(
                    $attributes->getHidden() ?? [],
                    $this->getHidden() ?? []
                )
            );
            // TODO : CREATE ATTRIBUTE FROM MODEL SERIALIZATION
            $this->setAttributes($attributes->toArray());
        } else {
            // TODO : CALL PARENT CONSTRUCTOR IF CONSTRUCTOR PARAMETER IS NOT INSTANCE OF Model::class
            parent::__construct($attributes);
        }
    }

    public function __call($name, $arguments)
    {
        $model = $this->getModel();
        if ($model) {
            return $this->proxy($model, $name, $arguments);
        }
        throw new \BadMethodCallException("Method $name does not exists on ".__CLASS__);
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->___model;
    }

    /**
     * @param mixed $model
     *
     * @return self
     */
    public function setModel($model)
    {
        if ($model) {
            $this->___model = $model;
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        $attributes = $this->attributesToArray();
        $hidden = array_merge($this->getHidden());
        // TODO: GET MODEL RELATIONS
        foreach ($this->___model->getRelations() as $key => $value) {
            if (in_array($key, $hidden)) {
                continue;
            }
            // #region TODO: Uncomment the code below to remove relations columns that
            // are specified in the _columns query parameters
            // TODO: Provide a better implementation to avoid performance heck or
            // remove implementation that strip hidden sub attributes as it can impact 
            // application performance for large datasets.
            $props = [];
            foreach ($hidden as $k => $v) {
                if (Str::startsWith($v, $key)) {
                    $props[] = Str::after("$key.", $v);
                    unset($hidden[$k]);
                    continue;
                }
            }
            $attributes[$key] = Arr::except($value->attributesToArray(), $props);
            // #endregion TODO
            // $attributes[$key] = $value;
        }
        return $attributes;
    }
}
