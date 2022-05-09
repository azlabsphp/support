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

interface TreeNode extends \JsonSerializable, NodeElement
{
    /**
     * Node children setter and getter interface.
     *
     * @param self[]|null $values
     *
     * @return self[]
     */
    public function childNodes(?array $values = null);

    /**
     * Getter and Setter Zero based level of the node in the tree structure.
     *
     * @param string|int|null $value
     *
     * @return int
     */
    public function level($value = null);
}
