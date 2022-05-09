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

namespace Drewlabs\Support\Tree;

interface NodeElement
{
    /**
     * The primary key of the node element.
     *
     * @return string|int
     */
    public function key();

    /**
     * Checks if the node is a root node.
     *
     * @return bool
     */
    public function isRoot();

    /**
     * returns the parent node of the current node.
     *
     * @return self
     */
    public function previous();
}
