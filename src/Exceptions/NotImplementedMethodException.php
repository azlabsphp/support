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

class NotImplementedMethodException extends \Exception
{
    /**
     * Creates a {@see NotImplementedMethodException} instance
     * 
     * @param mixed $method 
     * @return static 
     */
    public function __construct($method)
    {
        $msg = sprintf('Not Implementation provided for %s', $method);
        parent::__construct($msg, 500);
    }
}
