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

use Drewlabs\Support\Immutable\ModelValueObject;

class TestModelValueObject extends ModelValueObject
{
    public function getLabelAttribute()
    {
        return strtoupper($this->getRawAttribute('label'));
    }

    protected function getJsonableAttributes()
    {
        return [
            'label',
            'comments',
            'title',
        ];
    }

    protected function setCommentsAttribute(?array $comments)
    {
        $this->setRawAttribute(
            'comments',
            array_map(
                static function ($comment) {
                    return [
                        'content' => $comment['description'] ?? null,
                    ];
                },
                $comments ?? []
            )
        );
    }

    protected function setTitleAttribute(?string $value)
    {
        $this->setRawAttribute('title', $value ? ucfirst(strtolower($value)) : $value);
    }
}
