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

namespace Drewlabs\Support\Immutable\Traits;

use Drewlabs\Contracts\Data\Model\Model;

trait HasModelAttribute
{
    /**
     * @var Model|mixed
     */
    private $___model;

    /**
     * @param Model|mixed $model
     *
     * @return static
     */
    public function withModel($model)
    {
        $this->___model = $model;

        return $this;
    }

    protected function getModel()
    {
        return $this->___model;
    }
}
