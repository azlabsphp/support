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
     * Creates a {@see NotImplementedMethodException} instance.
     *
     * @return static
     */
    public function __construct(string $method)
    {
        parent::__construct(sprintf('No implementation provided for %s', $method), 500);
    }
}
