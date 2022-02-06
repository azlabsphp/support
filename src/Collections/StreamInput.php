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

namespace Drewlabs\Support\Collections;

class StreamInput
{
    public static function wrap($source, $accepts = true)
    {
        return new class($source, $accepts) {
            /**
             * @var \Closure|bool
             */
            private $predicate;

            /**
             * @var mixed
             */
            public $value;

            public function __construct($value, $predicate)
            {
                $this->value = $value;
                $this->predicate = $predicate;
            }

            public function accepts()
            {
                return \is_bool($this->predicate) ?
                    $this->predicate :
                    \call_user_func($this->predicate, $this->value);
            }
        };
    }
}
