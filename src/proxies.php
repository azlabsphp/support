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

namespace Drewlabs\Support\Proxy;

use Drewlabs\Contracts\Support\DataTransfertObject\QueryResultInterface;
use Drewlabs\Support\Actions\Action;
use Drewlabs\Support\Actions\ActionResult;
use Drewlabs\Support\Collections\SimpleCollection;
use Drewlabs\Support\Collections\Stream;
use Drewlabs\Support\Compact\DataTransfertObjectBridge;
use Drewlabs\Support\Tree\TreeView;
use Drewlabs\Support\XML\XMLAttribute;
use Drewlabs\Support\XML\XMLElement;

/**
 * Provides a proxy interface to {@link Action} class constructor.
 *
 * @param array|object $attributes
 *
 * @return Action
 */
function Action($attributes = [])
{
    return new Action($attributes);
}

/**
 * Provides a proxy interface to {@link ActionResult} class constructor.
 *
 * @return ActionResult
 */
function ActionResult($value = null)
{
    return new ActionResult($value);
}

/**
 * Provides a proxy interface to {@link XMLElement} class constructor.
 *
 * @param string|XMLElement|null  $value
 * @param array|XMLAttribute|null $attributes
 *
 * @return XMLElement
 */
function XMLElement(
    string $name,
    $value = '',
    string $ns = '',
    $attributes = [],
    ?string $xmlns = null
) {
    return new XMLElement($name, $value, $ns, $attributes, $xmlns);
}

/**
 * Provides a proxy interface to {@link XMLAttribute} class constructor.
 *
 * @param string $value
 *
 * @return XMLAttribute
 */
function XMLAttribute(string $name, $value = '')
{
    return new XMLAttribute($name, $value);
}

// Value object proxies

/**
 * Provides a proxy interface to {@link DataTransfertObjectBridge} class constructor.
 *
 * @return QueryResultInterface
 */
function DataTransfertObjectBridge($type = null)
{
    return new DataTransfertObjectBridge($type);
}

#region Data structures
/**
 * Provides a proxy interface to {@link SimpleCollection} class constructor.
 *
 * @return SimpleCollection
 */
function Collection($items = [])
{
    return new SimpleCollection($items);
}

/**
 * Creates a stream object from source elements
 * 
 * @param array|\iterable|\Iterator $source
 * @return Stream 
 */
function Stream($source = [])
{
    return Stream::of($source);
}

/**
 * Creates a ranged stream using user provided parameters. It's similary
 * to PHP built-in {@see range()} function but does not allocate memory, instead
 * creates an iterator to the list of values
 * 
 * @param int $start 
 * @param int $end 
 * @param int $steps 
 * @return Stream 
 */
function RangeStream(int $start, int $end, $steps = 1)
{
    return Stream::range($start, $end, $steps);
}

/**
 * Creates a tree view from the provided list of object|values
 * 
 * @param array|\itreable|\Iterator $values 
 * @return \Drewlabs\Support\Tree\TreeNode[] 
 */
function TreeView($values = [])
{
    $view = new TreeView($values);
    return $view->build();
}
#region Data structures