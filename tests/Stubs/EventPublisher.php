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

class EventPublisher implements Publisher
{
    /**
     * @var bool
     */
    private $state;

    public function __construct(bool $state)
    {
        $this->state = $state;
    }

    public function setState(bool $state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function send($event)
    {
        print_r($event);
    }
}
