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

namespace Drewlabs\Support\Compact;

use Drewlabs\Contracts\Support\DataTransfertObject\ObjectInterface;
use Drewlabs\Support\Exceptions\JsonEncodingException;
use Drewlabs\Support\Immutable\ValueObject;

abstract class AbstractDataTransfertObject extends ValueObject implements ObjectInterface
{
    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        if (method_exists($this, 'toArray')) {
            return \call_user_func([$this, 'toArray']);
        }

        return parent::jsonSerialize();
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);
        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forDtoObject($this, json_last_error_msg());
        }

        return $json;
    }

    public function serialize()
    {
        return parent::attributesToArray();
    }
}
