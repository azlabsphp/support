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

namespace Drewlabs\Support\Tests\Stubs;

use Drewlabs\Core\Helpers\Reflector;
use Drewlabs\Support\Traits\ImmutableConfig;

class ConfigurationManagerStub2
{
    use ImmutableConfig;

    public static function customConfigure(array $config, string $other_params)
    {
        $self = Reflector::propertySetter('config', $config ?? [])(new static());
        static::$instance = $self;

        return $self;
    }
}
