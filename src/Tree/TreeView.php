<?php

namespace Drewlabs\Support\Tree;

/**
 * Static class implementation for creating a recursive tree view
 * from a list of user nodes
 * 
 * @package Drewlabs\Support\Tree
 */
final class TreeView
{
    /**
     * 
     * @var TreeNode[]
     */
    private $internal;

    /**
     * 
     * @param NodeElement[]|array<array<string,mixed>> $values 
     * @return self 
     */
    public function __construct(array $values)
    {
        $this->internal = $this->map(
            array_filter(
                $values ?? [],
                function ($value) {
                    return null !== $value && (($value instanceof NodeElement) || is_array($value));
                }
            ),
            function ($value) {
                return is_array($value) ?
                    new Node($value['id'] ?? null, $value, $value['parent']) :
                    new Node($value->key(), $value, $value->previous());
            }
        );
    }

    /**
     * Build a tree view from the internal list
     * 
     * @return TreeNode[] 
     */
    public function build()
    {
        return static::create($this->internal);
    }

    /**
     * Groups list by a given value
     *
     * @param \Iterator|array $values 
     * @param string|int $groupBy 
     * @return array 
     */
    private static function groupBy($values, $groupBy)
    {
        $groupBy =  (!is_string($groupBy) && is_callable($groupBy)) ?
            $groupBy :
            function ($value) use ($groupBy) {
                if (is_array($value)) {
                    return $value[$groupBy] ?? null;
                }
                if (is_object($groupBy)) {
                    return $value->{$groupBy};
                }
                return $value;
            };
        $results = [];
        foreach ($values as $key => $value) {
            $groupKeys = $groupBy($value, $key);

            if (!is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }
            foreach ($groupKeys as $groupKey) {
                if (!array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = [];
                }
                $results[$groupKey][] = $value;
            }
        }
        return $results;
    }

    /**
     * Creates a recursive tree view from a list of user provided nodes
     * 
     * @param TreeNode[] $list 
     * @return TreeNode[]
     */
    public static function create(array $list)
    {
        // Build folders tree structure from a list of folders using the BFS algorithm
        // Group the folders by parent id in order to ease the search algorithm
        $groups = static::groupBy(
            $list,
            function (TreeNode $node) {
                return $node->previous();
            }
        );
        // Get the top node of the tree structure
        $topNodes = array_filter(
            $list,
            function (TreeNode $value) {
                return $value->isRoot();
            }
        );
        /**
         * @return TreeNode[]
         */
        $getChildNodes = function ($index) use ($groups) {
            return $groups[$index] ?? [];
        };
        // Get the child nodes for a provided parent node while the parent node
        // still have child node using recursion algorithm
        /**
         * @param TreeNode $node
         */
        $buildTree = function ($node) use (&$buildTree, &$getChildNodes) {
            /**
             * @var TreeNode[]
             */
            $nodes = $getChildNodes($node->key()) ?? [];
            // We compute the next level value by incrementing the parent level by 1
            $level = ($node->level() ?? 0) + 1;
            $node->childNodes(
                static::map(
                    array_filter($nodes),
                    function ($node_) use (&$buildTree, $level) {
                        // Here we set the child node level
                        $node_->level($level);
                        return $buildTree($node_);
                    }
                )
            );
            return $node;
        };
        return static::map(
            $topNodes,
            function (TreeNode $node) use (&$buildTree) {
                $node->level(0);
                return $buildTree($node);
            }
        );
    }

    private static function map(
        array $items,
        callable $callback,
        bool $preserveKeys = false
    ) {

        $keys = array_keys($items);
        $items = array_map($callback, $items, $keys);
        return $preserveKeys ? array_combine($keys, $items) : array_values($items);
    }
}
