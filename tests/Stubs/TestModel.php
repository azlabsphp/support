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

use Drewlabs\Contracts\Data\Model\Model;

class TestModel implements Model
{
    public function getPrimaryKey()
    {
        return 'id';
    }

    public function setKey($value)
    {
    }

    public function getKey()
    {
        return 1;
    }

    public function getTable()
    {
        return 'examples';
    }

    public function attributesToArray()
    {
        return [
            'label' => 'Hello World!',
        ];
    }

    public function getAttributes()
    {
        return [
            'label' => 'Hello World!',
        ];
    }

    public function toArray()
    {
        return [
            'title' => 'Welcome to IT World',
            'label' => 'Hello World!',
            'comments' => [
                [
                    'title' => 'HW issues',
                    'description' => 'Hello World issues',
                ],
            ],
        ];
    }

    public function getHidden()
    {
        return [];
    }

    public function setHidden(array $values)
    {
    }
}
