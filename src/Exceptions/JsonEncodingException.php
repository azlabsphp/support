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

namespace Drewlabs\Support\Exceptions;

use Drewlabs\Contracts\EntityObject\IDtoObject;

class JsonEncodingException extends \InvalidArgumentException
{
    /**
     * Create a new JSON encoding exception for the resource.
     *
     * @param IDtoObject $object
     * @param string     $message
     *
     * @return static
     */
    public static function forDtoObject($object, $message)
    {
        $model = $object->toModel();
        return new static('Error encoding resource ['.\get_class($object).'] with model ['.\get_class($model).'] with ID ['.$model->getKey().'] to JSON: '.$message);
    }
}
