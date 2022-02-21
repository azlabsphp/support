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

namespace Drewlabs\Support\Tests\Unit;

use Drewlabs\Support\Tests\Stubs\TestModel;
use Drewlabs\Support\Tests\Stubs\TestModelValueObject;
use Drewlabs\Support\Tests\TestCase;

class ModelValueObjectTest extends TestCase
{
    public function testGetLabelDynamicMethod()
    {
        $model = new TestModelValueObject(new TestModel());
        $this->assertSame($model->label, 'HELLO WORLD!', 'Expect label attribute getter to return HELLO WORLD!');
    }

    public function testSetCommentsDynamicMethod()
    {
        $model = new TestModelValueObject(new TestModel());
        $this->assertSame($model->comments[0]['content'], 'Hello World issues');
    }

    public function testSetTitleDynamicMethod()
    {
        $model = new TestModelValueObject(new TestModel());
        $this->assertSame($model->title, 'Welcome to it world');
    }
}
