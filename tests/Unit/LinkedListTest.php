<?php

namespace Drewlabs\Support\Tests\Unit;

use ArrayIterator;
use Drewlabs\Support\Collections\LinkedList;
use Drewlabs\Support\Collections\Stream;
use Drewlabs\Support\Tests\TestCase;

class LinkedListTest extends TestCase
{

    public function test_contructor()
    {
        $list = new LinkedList;
        $this->assertInstanceOf(LinkedList::class, $list, 'Expect the constructor to run successfully');
    }

    public function test_list_grow_when_push_is_called()
    {
        $list = new LinkedList;
        $list->push(1);
        $list->push(2);
        $this->assertEquals(2, $list->size());
    }

    public function test_is_empty_returs_false_if_item_pushed_to_list()
    {
        $list = new LinkedList;
        $this->assertTrue($list->isEmpty());
        $list->push(2);
        $this->assertFalse($list->isEmpty());
    }

    public function test_list_shrink_when_pop_is_called()
    {
        $list = new LinkedList;
        $list->push(1);
        $list->push(2);
        $this->assertEquals(2, $list->size());
        $result = $list->pop();
        $this->assertEquals(2, $result);
        $this->assertEquals(1, $list->size());
    }

    public function test_list_first()
    {
        $list = new LinkedList;
        $this->assertNull($list->first());
        $list->push(1);
        $list->push(2);
        $this->assertEquals(1, $list->first());
    }

    public function test_list_last()
    {
        $list = new LinkedList;
        $this->assertNull($list->last());
        $list->push(1);
        $list->push(2);
        $this->assertEquals(2, $list->last());
    }

    public function test_list_clear()
    {
        $list = new LinkedList;
        $list->push(1);
        $list->push(2);
        $list->clear();
        $this->assertEquals(0, $list->size());
    }

    public function test_create_list_from_iterator()
    {
        $list = new LinkedList(new ArrayIterator([1,2,3,4,5]));
        $this->assertTrue($list->size() === 5);
        $this->assertEquals(1, $list->first());
        $this->assertEquals(5, $list->last());
    }

    public function test_list_to_iterator()
    {
        $list = new LinkedList(new ArrayIterator([1,2,3,4,5]));
        $this->assertInstanceOf(\Traversable::class, $list->getIterator());
    }

    public function test_list_to_stream()
    {
        $list = new LinkedList(new ArrayIterator([1,2,3,4,5]));
        $this->assertInstanceOf(Stream::class, $list->stream());
    }

    public function test_list_to_array()
    {
        $list = new LinkedList(new ArrayIterator([1,2,3,4,5]));
        $this->assertEquals([1,2,3,4,5], $list->toArray());
    }

    public function test_value_at()
    {
        $list = new LinkedList(new ArrayIterator([1,2,3,4,5]));
        $this->assertEquals(3, $list->at(2));
        $this->assertNull($list->at(5));
    }

    public function test_find_index()
    {
        $list = new LinkedList(new ArrayIterator([1,2,3,4,5]));
        $this->assertEquals(2, $list->find(3));
        $this->assertEquals(2, $list->find(function($value) {
            return $value === 3;
        }));
        $this->assertEquals(-1, $list->find(10));
    }
}