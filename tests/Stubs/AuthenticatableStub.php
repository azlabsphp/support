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

use Drewlabs\Core\Auth\Traits\Authenticatable;
use Drewlabs\Core\Auth\User;

class AuthenticatableStub extends User
{
    use Authenticatable;

    public function getFillables()
    {
        return [
            'username',
            'password',
        ];
    }

    public function getGuarded()
    {
        return [];
    }

    public function createToken($name, array $scopes = [])
    {
        return drewlabs_core_random_app_key(128);
    }
}
