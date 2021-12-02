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

class UnsupportedConfigurationFileException extends \Exception
{
    public function __construct(array $supportedExtensions = [])
    {
        $message = sprintf('Supported configuration files are %s', drewlabs_core_strings_from_array($supportedExtensions));

        parent::__construct($message, 500);
    }
}
