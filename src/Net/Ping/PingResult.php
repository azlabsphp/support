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

namespace Drewlabs\Support\Net\Ping;

class PingResult
{
    /**
     * @var float
     */
    private $latency;

    /**
     * @var string|null
     */
    private $output;

    /**
     * @var string
     */
    private $error;

    /**
     * @param float|bool $latency
     *
     * @return void
     */
    public function __construct($latency, ?string $output = null, ?string $error = null)
    {
        $this->latency = $latency;

        $this->output = $output;

        $this->error = $error;
    }

    public function latency()
    {
        return $this->latency;
    }

    public function output()
    {
        $this->output;
    }

    public function error()
    {
        $this->error;
    }
}
