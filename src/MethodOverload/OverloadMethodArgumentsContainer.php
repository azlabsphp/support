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

namespace Drewlabs\Support\MethodOverload;

use Drewlabs\Contracts\Support\FuncArgument;
use Drewlabs\Support\Immutable\ValueObject;

class OverloadMethodArgumentsContainer extends ValueObject
{
    public function count()
    {
        return \count($this->getAll() ?? []);
    }

    public function length()
    {
        return $this->count();
    }

    /**
     * @return FuncArgument[]
     */
    public function getAll()
    {
        return $this->all;
    }

    /**
     * @return FuncArgument[]
     */
    public function getRequiredArguments()
    {
        return $this->required;
    }

    /**
     * @return FuncArgument[]
     */
    public function getOptionalArguments()
    {
        return $this->optional;
    }

    /**
     * @return int
     */
    public function requiredArgumentsCount()
    {
        return $this->required_count;
    }

    /**
     * @return int
     */
    public function optionalArgumentsCount()
    {
        return $this->optional_count;
    }

    protected function getJsonableAttributes()
    {
        return [
            'all',
            'required',
            'optional',
            'required_count',
            'optional_count',
        ];
    }
}
