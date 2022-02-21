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

namespace Drewlabs\Support\Tests\Stubs;

use Drewlabs\Support\Traits\ImmutableConfigurationManager;

class ConfigurationManagerStub2
{
    use ImmutableConfigurationManager;

    public static function customConfigure(array $config, string $other_params)
    {
        $self = drewlabs_core_create_attribute_setter('config', $config ?? [])(new static());
        static::$instance = $self;

        return $self;
    }
}
