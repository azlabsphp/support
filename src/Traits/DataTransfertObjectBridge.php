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

namespace Drewlabs\Support\Traits;

use Drewlabs\Contracts\Support\ArrayableInterface;
use Drewlabs\Contracts\Support\DataTransfertObject\ObjectInterface;

trait DataTransfertObjectBridge
{
    /**
     * @var string
     */
    private $type_ = ObjectInterface::class;

    /**
     * @var mixed
     */
    private $class;

    /**
     * Object initializer. Implementation requires a class name for dynamically building dto object instance.
     */
    public function __construct($type = null)
    {
        if (null !== $type) {
            $this->bind($type);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function bindClass($objectClass)
    {
        $this->class = $objectClass;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function bind($type)
    {
        if (!\is_string($type) && !\is_object($type)) {
            throw new \InvalidArgumentException();
        }
        $this->setType(\is_string($type) ? $type : \get_class($type));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toObject($model, $all = false)
    {
        if (!\is_array($model)) {
            $hasToArrayMethod = \is_object($model) && !($model instanceof ArrayableInterface) && !method_exists($model, 'toArray');
            if ($hasToArrayMethod) {
                return $this->createTemplateClass()->fromStdClass($model);
            }
            $attributes = \is_array($model) ? $model : $model->toArray();
        } else {
            $attributes = $model;
        }

        return $this->createTemplateClass()->copyWith($attributes, $all);
    }

    /**
     * {@inheritDoc}
     */
    public function objectToModel($value)
    {
        /**
         * @var ObjectInterface
         */
        $value = \is_object($value) && !($value instanceof ObjectInterface) ? $this->createTemplateClass()->fromStdClass($value) : $value;

        return $value->toModel();
    }

    /**
     * {@inheritDoc}
     */
    public function objectToModelList(array $list)
    {
        return array_map(function ($i) {
            // code...
            return $this->objectToModel($i);
        }, $list);
    }

    /**
     * {@inheritDoc}
     */
    public function toObjectList(array $values, $all = false)
    {
        return array_map(function ($i) use ($all) {
            // code...
            return $this->toObject($i, $all);
        }, $values);
    }

    /**
     * Create binding class instance.
     *
     * @return ObjectInterface
     */
    private function createTemplateClass()
    {
        return $this->ensureBindings()
            ->validate(new $this->class());
    }

    private function ensureBindings()
    {
        if (null === $this->class) {
            throw new \RuntimeException('Call the bind() method before performing any action');
        }

        return $this;
    }

    private function validate($value)
    {
        $type = $this->getType() ?? ObjectInterface::class;
        if (!($value instanceof $type)) {
            throw new \RuntimeException('Class passed to contructor must be an instance of '.ObjectInterface::class.', '.\get_class($value).' object given');
        }

        return $value;
    }

    private function setType(string $type)
    {
        $this->type_ = $type;
    }

    private function getType()
    {
        return $this->type_;
    }
}
