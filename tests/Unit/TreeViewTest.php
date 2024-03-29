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

namespace Drewlabs\Support\Tests\Unit;

use Drewlabs\Support\Tests\TestCase;
use Drewlabs\Support\Tree\Node;
use Drewlabs\Support\Tree\TreeNode;
use Drewlabs\Support\Tree\TreeView;

class TreeViewTest extends TestCase
{
    public function testTreeViewCreate()
    {
        $list = [
            new Node(1, null, null),
            new Node(2, null, 1),
            new Node(3, null, 1),
            new Node(4, null, 2),
            new Node(5, null, 2),
            new Node(6, null, 2),
            new Node(7, null, 3),
            new Node(8, null, 3),
            new Node(9, null, 3),
            new Node(10, null, 4),
            new Node(11, null, 6),
        ];
        $view = TreeView::create($list);
        $this->assertInstanceOf(TreeNode::class, $view[0]);
        $this->assertTrue(2 === \count($view[0]->childNodes()));
        $this->assertTrue(0 === $view[0]->level());
        // Test Chil nodes levels
        $children = $view[0]->childNodes();
        foreach ($children as $value) {
            $this->assertInstanceOf(TreeNode::class, $value);
            $this->assertTrue(1 === $value->level());
        }
    }

    public function testCreateTreeViewFromArray()
    {
        $object = new TreeView([
            ['id' => 1, 'parent' => null],
            ['id' => 2, 'parent' => 1],
            ['id' => 3, 'parent' => 1],
            ['id' => 4, 'parent' => 2],
            ['id' => 5, 'parent' => 2],
            ['id' => 6, 'parent' => 2],
            ['id' => 7, 'parent' => 3],
            ['id' => 8, 'parent' => 3],
            ['id' => 9, 'parent' => 3],
            ['id' => 10, 'parent' => 4],
            ['id' => 11, 'parent' => 6],
        ]);
        $view = $object->build();
        $this->assertInstanceOf(TreeNode::class, $view[0]);
        $this->assertTrue(2 === \count($view[0]->childNodes()));
        $this->assertTrue(0 === $view[0]->level());
        // Test Chil nodes levels
        $children = $view[0]->childNodes();
        foreach ($children as $value) {
            $this->assertInstanceOf(TreeNode::class, $value);
            $this->assertTrue(1 === $value->level());
        }
    }
}
