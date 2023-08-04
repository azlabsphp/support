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

namespace Drewlabs\Support\Tests\Unit;

use Drewlabs\Support\PackagesConfigurationManifest;
use Drewlabs\Support\Tests\Stubs\ConfigurationManagerStub1;
use Drewlabs\Support\Tests\Stubs\ConfigurationManagerStub2;
use Drewlabs\Support\Tests\TestCase;

class PackageConfigurationManifestTest extends TestCase
{
    public function testLoadPHPConfigurationMethod()
    {
        PackagesConfigurationManifest::load([
            ConfigurationManagerStub1::class => __DIR__.'/../Stubs/config/config_stub_1.php',
            ConfigurationManagerStub2::class => [
                'customConfigure',
                [
                    __DIR__.'/../Stubs/config/config_stub_1.php',
                    'Hello',
                ],
            ],
        ]);
        $this->assertTrue('./src' === ConfigurationManagerStub2::getInstance()->get('autoload.psr-4.Package', null));
    }

    public function testLoadJSONConfigurationMethod()
    {
        PackagesConfigurationManifest::load([
            ConfigurationManagerStub1::class => __DIR__.'/../Stubs/config/config_stub_1.json',
        ]);
        $this->assertTrue('./src' === ConfigurationManagerStub1::getInstance()->get('autoload.psr-4.Package', null));
    }
}
