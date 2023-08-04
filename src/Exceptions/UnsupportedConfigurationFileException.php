<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Support\Exceptions;

class UnsupportedConfigurationFileException extends \Exception
{
    /**
     * Creates class instance.
     */
    public function __construct(array $extensions = [])
    {
        parent::__construct(sprintf('Supported configuration files are %s', implode(', ', $extensions)), 500);
    }
}
