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

use Drewlabs\Support\Collections\SimpleCollection;
use Drewlabs\Support\Exceptions\NotFoundException;

use function Drewlabs\Support\Proxy\Collection;

use Drewlabs\Support\Tests\TestCase;

class SimpleCollectionTest extends TestCase
{
    public function testFactoryFunctions()
    {
        $collection = SimpleCollection::fromArray([
            'english' => 'Hello!',
            'french' => 'Salut!',
            'spanish' => 'Hola!',
            'latin' => 'Salve!',
            'german' => 'Guten Tag!',
        ]);

        $this->assertInstanceOf(SimpleCollection::class, $collection, 'Expect the created collection to be an instance of SimpleCollection class');
    }

    public function testAddMethod()
    {
        $collection = SimpleCollection::fromArray([
            'english' => 'Hello!',
            'french' => 'Salut!',
            'spanish' => 'Hola!',
            'latin' => 'Salve!',
            'german' => 'Guten Tag!',
        ]);

        $collection->add('chinese', 'Nǐn hǎo');
        $this->assertTrue($collection->offsetExists('chinese'), 'Expect the chineese to exists in collection keys');
    }

    public function testGetMethod()
    {
        $collection = SimpleCollection::fromArray([
            'english' => 'Hello!',
            'french' => 'Salut!',
            'spanish' => 'Hola!',
            'latin' => 'Salve!',
            'german' => 'Guten Tag!',
        ]);
        $this->assertSame($collection->get('latin'), 'Salve!', 'Expect key latin value to equals Savle!');
        $this->assertSame($collection->get(0), 'Hello!', 'Expect first item to equals Hello!');

        // Test for numerics
        $collection1 = SimpleCollection::fromArray([1, 4, 6, 10, 78]);
        $this->assertSame($collection1->get(4), 78, 'Expect the fourth element to equals 78');

        $collection2 = SimpleCollection::fromArray(array_values($collection->all()));

        $this->assertSame($collection2->get(4), 'Guten Tag!', 'Expect the fourth element of collection2 to equals <<Guten Tag!>>');

        $this->assertSame($collection->get(4), 'Guten Tag!', 'Expect the fourth element of collection to equals <<Guten Tag!>>');

        $this->assertSame($collection->get(static function ($list) {
            return $list[2];
        }, null), 'Hola!', 'Expect the function to return <<Hola!>>');
    }

    public function testSizeMethod()
    {
        $collection = SimpleCollection::fromArray([
            'english' => [
                'greetings' => 'Hello!',
            ],
            'french' => [
                'greetings' => 'Salut!',
            ],
            'spanish' => [
                'greetings' => 'Hola!',
            ],
            'latin' => [
                'greetings' => 'Salve!',
            ],
            'german' => [
                'greetings' => 'Guten Tag!',
            ],
        ]);
        $this->assertTrue(5 === $collection->size(), 'Expect collection to contain 5 elements');
    }

    public function testClearAndEmptyMethods()
    {
        $collection = SimpleCollection::fromArray([
            'english' => 'Hello!',
            'french' => 'Salut!',
            'spanish' => 'Hola!',
            'latin' => 'Salve!',
            'german' => 'Guten Tag!',
        ]);
        $collection->clear();

        $this->assertTrue($collection->values()->isEmpty(), 'Expect collection values to return an empty array');
        $this->assertTrue($collection->isEmpty(), 'Expect the collection to be empty');
    }

    public function testRemoveMethod()
    {
        $collection = SimpleCollection::fromArray([
            'english' => 'Hello!',
            'french' => 'Salut!',
            'spanish' => 'Hola!',
            'latin' => 'Salve!',
            'german' => 'Guten Tag!',
        ]);
        $collection->remove(1);
        $this->assertTrue(null === $collection->get('french'), 'Expect collection french key to net be in the list');
        $this->assertTrue(4 === $collection->size(), 'Expect the size of the collection to equals 4');
    }

    // public function testIteratorMethod()
    // {
    //     $collection = SimpleCollection::fromArray([
    //         'english' => 'Hello!',
    //         'french' => 'Salut!',
    //         'spanish' => 'Hola!',
    //         'latin' => 'Salve!',
    //         'german' => 'Guten Tag!'
    //     ]);
    //     $this->assertTrue(true);
    // }

    /**
     * @return void
     */
    public function testAddAllMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $collection2 = SimpleCollection::fromArray([
            'salve' => 'Salve!',
            'Guten Tag!',
        ]);

        $collection = SimpleCollection::fromArray([
            'Hello!',
            'Salut!',
            'Hola!',
        ])->addAll($collection2);
        $this->assertTrue(5 === $collection->size(), 'Expect the size of the new collection to equals 5');
        $this->assertSame($collection->get(2), 'Hola!', 'Expect the second item in the collection to equals <<Hola!>>');
    }

    public function testFirstMethod()
    {
        $collection2 = SimpleCollection::fromArray([
            'english' => 'Hello!',
            'french' => 'Salut!',
            'spanish' => 'Hola!',
            'latin' => 'Salve!',
            'german' => 'Guten Tag!',
        ]);
        $this->assertTrue('Hello!' === $collection2->first(), 'Expect the first item to equals Salve!');
    }

    public function testLastMethod()
    {
        $collection2 = SimpleCollection::fromArray([
            'english' => 'Hello!',
            'french' => 'Salut!',
            'spanish' => 'Hola!',
            'latin' => 'Salve!',
            'german' => 'Guten Tag!',
        ]);
        $this->assertTrue('Guten Tag!' === $collection2->last(), 'Expect the first item to equals Guten Tag!');
    }

    public function testCombineMethod()
    {
        // $this->expectException(\InvalidArgumentException::class);
        $collection = SimpleCollection::fromArray([
            'Hello!',
            'Salut!',
            'Hola!',
            'Salve!',
            'Guten Tag!',
        ]);

        $collection->combine([
            'english',
            'french',
            'spanish',
            'latin',
            'german',
        ]);
        $this->assertTrue('english' === $collection->keys()->first());
    }

    public function testFilterMethod()
    {
        /**
         * @var SimpleCollection
         */
        $collection = SimpleCollection::fromArray([
            'Hello!',
            'Salut!',
            'Hola!',
            'Salve!',
            'Guten Tag!',
        ]);

        $collection->combine([
            'english',
            'french',
            'spanish',
            'latin',
            'german',
        ]);
        $collection = $collection->filter(static function ($value, $key) {
            return \in_array($key, ['english', 'french'], true);
        });
        $this->assertSame(2, $collection->count());
    }

    public function testMapMethod()
    {
        /**
         * @var SimpleCollection
         */
        $collection = SimpleCollection::fromArray([
            'Hello!',
            'Salut!',
            'Hola!',
            'Salve!',
            'Guten Tag!',
        ]);

        $collection->combine([
            'english',
            'french',
            'spanish',
            'latin',
            'german',
        ]);

        $collection = $collection->map(
            static function ($value, $key) {
                return strtoupper($value);
            },
            true
        );
        $this->assertTrue('HELLO!' === $collection->first());
    }

    public function testReduceMethod()
    {
        /**
         * @var SimpleCollection
         */
        $collection = SimpleCollection::fromArray([
            'Hello!',
            'Salut!',
            'Hola!',
            'Salve!',
            'Guten Tag!',
        ]);

        $result = $collection->reduce(
            static function ($carry, $value) {
                $carry .= "|$value";

                return $carry;
            },
            ''
        );
        $this->assertSame('|Hello!|Salut!|Hola!|Salve!|Guten Tag!', $result);
    }

    // #region illuminate collection methods tests
    public function testEachMethod()
    {
        $count = 0;
        $collection = Collection(Collection([1, 3, 9, 10, 45]));
        $collection->each(static function () use (&$count) {
            ++$count;
        });
        $this->assertTrue($count === $collection->count());
    }

    public function testPadMethod()
    {
        $collection = Collection();
        $collection = $collection->pad(3, 0);
        $this->assertTrue(3 === $collection->count());
        $this->assertTrue(0 === $collection->first() && 0 === $collection->last());
    }

    public function testZipMethod()
    {
        $collection = Collection([1, 2, 3, 4]);
        $collection = $collection->zip(Collection([5, 6, 7, 8]));
        $this->assertTrue($collection->first() === [1, 5]);
        $this->assertTrue($collection->last() === [4, 8]);
    }

    public function testUniqueMethod()
    {
        $collection = Collection([1, 16, 1, 4, 2, 3, 4]);
        $collection = $collection->unique();
        $this->assertTrue(5 === $collection->count());
    }

    public function testTransformMethod()
    {
        $collection = Collection([1, 16, 1, 4, 2, 3, 4]);
        $collection = $collection->unique()->transform(static function ($value) {
            return $value * 2;
        });
        $this->assertTrue(32 === $collection->get(1));
    }

    public function testTakeMethod()
    {
        $collection = Collection([
            'Hello!',
            'Salut!',
            'Hola!',
            'Salve!',
            'Guten Tag!',
        ]);
        $this->assertTrue(2 === $collection->take(2)->count());
    }

    public function testTakeUntilMethod()
    {
        $collection = Collection([
            'Hello!',
            'Salut!',
            'Hola!',
            'Salve!',
            'Guten Tag!',
        ]);
        $this->assertTrue(3 === $collection->takeUntil(
            static function ($value, $key) {
                return 'Salve!' === $value;
            }
        )->count());
    }

    public function testSpliceMethod()
    {
        $collection = Collection([
            'Hello!',
            'Salut!',
            'Hola!',
        ]);
        $collection = $collection->splice(1, 3, [
            'Salve!',
            'Guten Tag!',
        ]);
        $this->assertTrue('Hola!' === $collection->get(1));
    }

    public function testFirstOrFailMethod()
    {
        $this->expectException(NotFoundException::class);
        $collection = Collection([
            'Hello!',
            'Salut!',
            'Hola!',
        ]);
        $this->assertTrue('Salut!' === $collection->firstOrFail('Guten Tag!'));
    }

    public function testChunkMethod()
    {
        $collection = Collection([
            'Hello!',
            'Salut!',
            'Hola!',
        ])->chunk(2);
        $this->assertTrue(2 === $collection->first()->count());
    }

    public function testCountByMethod()
    {
        $collection = Collection([
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ])->countBy('lang');
        $this->assertTrue(2 === $collection->get('Hola!'));
    }

    public function testChunkWhileMethod()
    {
        $collection = Collection([
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ])->chunkWhile(static function ($value) {
            return 3 !== $value['id'];
        });
        $this->assertTrue(true);
    }

    public function testTakeWhileMethod()
    {
        $collection = Collection([
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ])->takeWhile(static function ($value) {
            return 3 !== $value['id'];
        });
        $this->assertTrue(2 === $collection->count());
    }

    public function testSplitInMethod()
    {
        $collection = Collection([
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ])->splitIn(2);
        $this->assertTrue(2 === $collection->first()->count());
    }

    public function testSkipMethods()
    {
        $initial = Collection([
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ]);
        $collection = $initial->skip(2);
        $this->assertTrue(3 === $collection->first()['id']);

        // Test skip until
        $collection = $initial->skipUntil(static function ($value) {
            return 'Hola!' === $value['lang'];
        });
        $this->assertTrue(1 === $collection->count());

        // Test skip while
        $collection = $initial->skipWhile(static function ($value) {
            return null !== $value['id'];
        });
        $this->assertTrue($collection->isEmpty());
    }

    public function testSliceMethod()
    {
        $collection = Collection([1, 2, 3, 4]);
        $collection = $collection->slice(1, 2, false);
        $this->assertTrue($collection->all() === [2, 3]);
    }

    // TOBE REVIEWED
    public function testSplitMethod()
    {
        $collection = Collection([1, 2, 3, 4]);
        $collection = $collection->split(4);
        $this->assertTrue(true);
    }

    // TOBE REVIEWED
    public function testShuffleMethod()
    {
        $collection = Collection(range(0, 100000))->shuffle();
        $this->assertTrue(1 !== $collection->first());
    }

    public function testReplaceMethods()
    {
        $collection = Collection([1, 2, 3, 4])->replace(Collection([5, 6, 7]));
        $this->assertTrue(5 === $collection->first());
        $collection = Collection([1, 2, 3, 4])->replaceRecursive(Collection([5, 6, 7]));
        $this->assertTrue(4 === $collection->last());
    }

    public function testReverseMethods()
    {
        $collection = Collection([1, 2, 3, 4])->reverse();
        $this->assertTrue(4 === $collection->first());
    }

    public function testSearchMethod()
    {
        $key = Collection([1, 2, 3, 4])->search(3);
        $this->assertTrue(2 === $key);
    }

    public function testShiftMethod()
    {
        $key = Collection([1, 2, 3, 4])->shift(1);
        $this->assertTrue(1 === $key);
        $collection = Collection([1, 2, 3, 4])->shift(2);
        $this->assertTrue($collection->all() === [1, 2]);
    }

    public function testAddMethods()
    {
        $source = [
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ];
        $initial = Collection($source);
        $initial->push(['id' => 4, 'lang' => 'Guten Tag!'], ['id' => 5, 'lang' => 'Ndiin!']);
        $this->assertSame('Ndiin!', $initial->get(4)['lang']);
        $collection = Collection($source);
        // The concat call creates a copy
        $modifiedCollection = $collection->concat([['id' => 4, 'lang' => 'Guten Tag!'], ['id' => 5, 'lang' => 'Ndiin!']]);
        $this->assertNotSame('Ndiin!', (null !== $value = $collection->get(4)) ? $value['lang'] : null);
        $this->assertSame('Ndiin!', $modifiedCollection->get(4)['lang']);
    }

    public function testMergeMethods()
    {
        $source = [
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ];
        $collection = Collection($source)->merge(Collection([['id' => 4, 'lang' => 'Guten Tag!'], ['id' => 5, 'lang' => 'Ndiin!']]));
        $this->assertTrue(5 === $collection->count());
    }

    public function testPullMethods()
    {
        $source = [
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ];
        $collection = Collection($source);
        $result = $collection->pull(1);
        $this->assertTrue(2 === $collection->count());
        $this->assertTrue($result === [
            'id' => 2,
            'lang' => 'Salut!',
        ]);
    }

    public function testNthMethod()
    {
        $collection = Collection(range(0, 20))->nth(2, 0, true);
        $this->assertTrue(11 === $collection->count());
    }

    public function testOnlyMethod()
    {
        $collection = Collection(range(0, 20))->only(Collection([3, 8, 10]));
        $this->assertTrue(3 === $collection->count());
    }

    public function testPopMethod()
    {
        $result = Collection(range(0, 20))->pop(1);
        $this->assertTrue(20 === $result);
        $collection = Collection(range(0, 20))->pop(2);
        $this->assertTrue($collection->all() === [20, 19]);
    }

    public function testPrependMethod()
    {
        $collection = Collection(range(0, 20))->prepend(21, '21');
        $this->assertTrue(21 === $collection->first());
    }

    public function testJoinMethod()
    {
        $result = Collection(range(0, 20))->join(',');
        $this->assertSame($result, '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20');
    }

    public function testHasMethod()
    {
        $result = Collection(range(0, 20))->has(10);
        $this->assertTrue($result);
    }

    public function testPluckMethod()
    {
        $result = Collection([
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ])->pluck('lang');
        $this->assertSame($result->first(), 'Hello!');
    }

    public function testIntersectMethod()
    {
        $result = Collection(range(0, 20))->intersect(range(10, 30));
        $this->assertSame(array_values($result->all()), range(10, 20));
        $result = Collection([
            'lang' => 'Hello!',
            'lang' => 'Salut!',
            'lang' => 'Hola!',
        ])->intersectByKeys(Collection([
            'lang' => 'Hola!',
        ]));
        $this->assertTrue(1 === $result->count());
    }

    public function testFlattenMethod()
    {
        $result = Collection([
            [
                'id' => 1,
                'lang' => 'Hello!',
            ],
            [
                'id' => 2,
                'lang' => 'Salut!',
            ],
            [
                'id' => 3,
                'lang' => 'Hola!',
            ],
        ])->flatten();
        $this->assertTrue(true);
    }

    public function testFlipMethod()
    {
        $result = Collection([
            'Hello!',
            'Salut!',
            'Hola!',
        ])->flip();
        $this->assertTrue($result->all() === array_flip([
            'Hello!',
            'Salut!',
            'Hola!',
        ]));
    }

    public function testForgetMethod()
    {
        $result = Collection([
            'Hello!',
            'Salut!',
            'Hola!',
        ])->forget(0, 1);
        $this->assertTrue('Hola!' === $result->first());
    }

    public function testGroupByMethod()
    {
        $result = Collection([
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
            ],
            [
                'id' => 'UUID-2',
                'lang' => 'Salut!',
            ],
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
            ],
        ])->groupBy('id', true);
        $this->assertTrue(2 === $result->get('UUID-2')->count());
        $result = Collection([
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
            ],
            [
                'id' => 'UUID-2',
                'lang' => 'Salut!',
            ],
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
            ],
        ])->keyBy('id');
    }

    public function testExceptMethod()
    {
        $result = Collection([
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
            ],
            [
                'id' => 'UUID-2',
                'lang' => 'Salut!',
            ],
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
            ],
        ])->except(1, 2);
        $this->assertTrue(1 === $result->count());
    }

    public function testMedianMethod()
    {
        $result = Collection(range(0, 20))->median();
        $this->assertTrue(10 === $result);
    }

    public function testCollapseMethod()
    {
        $result = Collection([
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
            ],
            [
                'id' => 'UUID-2',
                'lang' => 'Salut!',
            ],
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
            ],
        ])->collapse();
        $this->assertTrue(true);
    }

    public function testSortMethods()
    {
        $result = Collection([
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
                'year' => '1970',
            ],
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
                'year' => '1960',
            ],
            [
                'id' => 'UUID-3',
                'lang' => 'Salut!',
                'year' => '1870',
            ],
        ])->sortBy(['year', 'id'])->pluck('id');
        $this->assertTrue('UUID-2' === $result->first());

        $collection = Collection([
            'french',
            'spanish',
            'latin',
            'english',
            'german',
        ])->sort();
        $this->assertTrue('english' === $collection->first());

        $collection = Collection([
            'french',
            'spanish',
            'latin',
            'english',
            'german',
        ])->sortDesc();
        $this->assertTrue('spanish' === $collection->first());
    }

    // Enumable testing
    public function testAverageMethod()
    {
        $result = Collection(range(0, 20))->average();
        $this->assertSame(10, $result);
        $result = Collection([
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
                'year' => 1970,
            ],
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
                'year' => 1960,
            ],
            [
                'id' => 'UUID-3',
                'lang' => 'Salut!',
                'year' => 1870,
            ],
        ])->average(static function ($value) {
            return (int) $value['year'];
        });
        $this->assertSame((1970 + 1960 + 1870) / 3, $result);
    }

    public function testSumMethod()
    {
        $result = Collection(range(0, 20))->sum(static function ($value) {
            return $value;
        });
        $this->assertSame(array_reduce(range(0, 20), static function ($carry, $curr) {
            $carry += $curr;

            return $carry;
        }, 0), $result);
    }

    public function testContainsMethod()
    {
        $result = Collection(range(0, 20))->contains('10');
        $this->assertTrue($result);
        $result = Collection(range(0, 20))->containsStrict('10');
        $this->assertFalse($result);
    }

    public function testEachSpreadMethod()
    {
        $result = Collection([[1, 2, 4], [5, 6, 7]])->eachSpread(static function (...$values) {
            // var_dump($values);
        });
        $this->assertTrue(true);
    }

    public function testEveryMethod()
    {
        $result = Collection(array_map('strval', range(0, 20)))->every(static function ($value) {
            return \is_string($value);
        });
        $this->assertTrue($result);
    }

    public function testFirstWhereMethod()
    {
        $collection = Collection([
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
                'year' => 1970,
            ],
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
                'year' => 1960,
            ],
            [
                'id' => 'UUID-3',
                'lang' => 'Salut!',
                'year' => 1870,
            ],
        ])->firstWhere('id', 'UUID-1');
        $this->assertSame(1960, $collection['year'] ?? null);
    }

    public function testMapSpreadMethod()
    {
        $result = Collection([1, 2, 3, 4])->mapSpread(static function (...$values) {
            return $values;
        });
        $result = Collection([[1, 2, 3, 4], [5, 6, 7]])->flatMap(static function ($value) {
            return array_map(static function ($value) {
                return $value * 2;
            }, $value);
        });
        $this->assertTrue(4 === $result->get(1));
    }

    public function testMinMaxMethods()
    {
        $result = Collection(range(0, 20))->min(static function ($value) {
            return $value;
        });
        $this->assertTrue(0 === $result);
        $result = Collection(range(0, 20))->max(static function ($value) {
            return $value;
        });
        $this->assertTrue(20 === $result);
    }

    public function testPaginationMethod()
    {
        $result = Collection(range(0, 20))->forPage(2, 5, false);
        $this->assertTrue($result->all() === [5, 6, 7, 8, 9]);
    }

    public function testWhenMethod()
    {
        $result = Collection(range(0, 20))->when(true)->map(static function ($value) {
            return $value * 2;
        })->get(2);
        $this->assertTrue(4 === $result);
        $result = Collection(range(0, 20))->whenEmpty()->map(static function ($value) {
            return $value * 2;
        })->get(2);
        $this->assertTrue(true);
    }

    public function testWhereMethods()
    {
        $collection = Collection([
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
                'year' => 1970,
            ],
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
                'year' => 1960,
            ],
            [
                'id' => 'UUID-3',
                'lang' => 'Salut!',
                'year' => 1870,
            ],
        ]);
        $this->assertSame(1960, $collection->where('id', 'UUID-1')->first()['year'] ?? null);
        $this->assertTrue(3 === $collection->whereNotNull('id')->count());
        $this->assertTrue(2 === $collection->whereIn('id', ['UUID-1', 'UUID-2'], false)->count());
        $this->assertTrue(1 === $collection->whereNotIn('id', ['UUID-1', 'UUID-2'], true)->count());
        $this->assertTrue(1 === $collection->whereBetween('year', [1965, 1975], false)->count());
        $this->assertTrue(2 === $collection->whereNotBetween('year', [1965, 1975], false)->count());
    }

    public function testPipeMethod()
    {
        $collection = Collection([
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
                'year' => 1970,
            ],
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
                'year' => 1960,
            ],
            [
                'id' => 'UUID-3',
                'lang' => 'Salut!',
                'year' => 1870,
            ],
        ]);
        $this->assertSame((1970 + 1960 + 1870) / 3, $collection->pipe(
            static function ($collection) {
                return $collection->pluck('year');
            },
            static function ($collection) {
                return $collection->average();
            }
        ));
    }

    public function testTapMethod()
    {
        $collection = Collection([
            [
                'id' => 'UUID-2',
                'lang' => 'Hola!',
                'year' => 1970,
            ],
            [
                'id' => 'UUID-1',
                'lang' => 'Hello!',
                'year' => 1960,
            ],
            [
                'id' => 'UUID-3',
                'lang' => 'Salut!',
                'year' => 1870,
            ],
        ]);

        $collection->tap(static function ($collection_) {
            $collection_->forget(1);
        });

        $this->assertSame($collection->count(), 3);
    }

    // #endregion illuminate collection methods tests
}
