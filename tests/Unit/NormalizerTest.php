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

namespace Drewlabs\Support\Tests\Unit;

use Drewlabs\Core\Helpers\Str;
use Drewlabs\Support\Normalizer\Normalizer;
use Drewlabs\Support\Tests\TestCase;

class NormalizerTest extends TestCase
{
    public function testCamelizeString()
    {
        $variable = 'my_extended_';
        $this->assertSame(Normalizer::camelCase($variable, '{[_]+}i'), Str::regexCamelize($variable, false), 'Expect the Normalizer and drewlabs_core_strings_as_camel_case to return the same values');
    }
}
