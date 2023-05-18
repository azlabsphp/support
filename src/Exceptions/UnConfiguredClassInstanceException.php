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

class UnConfiguredClassInstanceException extends \Exception
{
    /**
     * Creates new class instance
     * 
     * @param string $blueprint 
     */
    public function __construct(string $blueprint)
    {
        parent::__construct(sprintf('%s instance not properly configured', $blueprint), 500);
    }
}
