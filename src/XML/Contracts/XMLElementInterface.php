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

use Drewlabs\Support\XML\XMLAttribute;

interface XMLElementInterface
{
    /**
     * Return a list of xml elements attributes.
     *
     * @return array|XMLAttribute[]
     */
    public function attributes();

    /**
     * Return the name of the xml element node.
     */
    public function name(): string;

    /**
     * Get the namespace (prefix) of the node.
     *
     * @return string
     */
    public function namespace();

    /**
     * Get the value of the element.
     *
     * @return string|static|static[]
     */
    public function value();
}
