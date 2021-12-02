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

namespace Drewlabs\Support\XML\Contracts;

interface XMLAttributeInterface
{
    /**
     * Return the name of the xml attribute node.
     */
    public function name(): string;

    /**
     * Get the value of the attribute.
     *
     * @return string|static|static[]
     */
    public function value(): string;
}
