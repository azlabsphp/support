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

namespace Drewlabs\Support\Tree;

use Drewlabs\Support\Tree\Traits\Node as TraitsNode;

class Node implements TreeNode
{
    use TraitsNode;

    /**
     * @param mixed                    $key
     * @param mixed                    $data
     * @param int|string|TreeNode|null $parent
     */
    public function __construct(
        $key,
        $data = null,
        $parent = null,
    ) {
        $this->__KEY__ = $key;
        $this->__STATE__ = $data;
        $this->__PARENT__ = $parent instanceof TreeNode ?
            $parent->key() :
            $parent;
    }
}
