<?php

require_once '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use function Dgame\Iterator\chain;
use function Dgame\Iterator\chars;
use function Dgame\Iterator\cycle;
use function Dgame\Iterator\iter;
use function Dgame\Iterator\only;

class IteratorTest extends TestCase
{
    public function testOptional()
    {
        $it = iter(['a', 'b', false, null, 0, 1]);

        $this->assertTrue($it->next()->isSome());
        $this->assertTrue($it->next()->isSome());
        $this->assertTrue($it->next()->isNone());
        $this->assertTrue($it->next()->isNone());
        $this->assertTrue($it->next()->isSome());
        $this->assertTrue($it->next()->isSome());
        $this->assertFalse($it->isValid());
    }

    public function testFilterEmpty()
    {
        $it = iter(['a', 'b', false, null, 0, 1])->filterEmpty();

        $this->assertEquals('a', $it->next()->unwrap());
        $this->assertEquals('b', $it->next()->unwrap());
        $this->assertEquals(1, $it->next()->unwrap());
        $this->assertFalse($it->isValid());
    }

    public function testOnly()
    {
        $it = only('a')->repeat(4);

        $this->assertEquals('aaaa', $it->implode());
    }

    public function testChars()
    {
        $it = chars('Hallo');

        $this->assertEquals('H', $it->current()->unwrap());
        $this->assertEquals('H', $it->next()->unwrap());
        $this->assertEquals('a', $it->next()->unwrap());
        $this->assertEquals('l', $it->next()->unwrap());
        $this->assertEquals('l', $it->next()->unwrap());
        $this->assertEquals('o', $it->next()->unwrap());
        $this->assertTrue($it->next()->isNone());
    }

    public function testCollect()
    {
        $it = chars('Hallo');

        $this->assertEquals(['H', 'a', 'l', 'l', 'o'], $it->collect());
        $this->assertEquals('Hallo', $it->implode());
    }

    public function testTake()
    {
        $it = chars('Hallo');
        $it = $it->take(2);

        $this->assertEquals('H', $it->next()->unwrap());
        $this->assertEquals('a', $it->next()->unwrap());
        $this->assertTrue($it->next()->isNone());
    }

    public function testSkip()
    {
        $it = chars('Hallo');
        $it = $it->skip(3);

        $this->assertEquals('l', $it->next()->unwrap());
        $this->assertEquals('o', $it->next()->unwrap());
        $this->assertTrue($it->next()->isNone());
    }

    public function testCycle()
    {
        $it = cycle([1, 2, 3])->take(2);

        $this->assertEquals([1, 2, 3, 1, 2, 3], $it->collect());
    }

    public function testChain()
    {
        $it = chain([1, 2, 3], ['a', 'b', 'c']);

        $this->assertEquals([1, 2, 3, 'a', 'b', 'c'], $it->collect());
    }

    public function testValues()
    {
        $this->assertEquals(iter(['a' => 'z', 'b' => 'y'])->values()->collect(), ['z', 'y']);
    }

    public function testKeys()
    {
        $this->assertEquals(iter(['a' => 'z', 'b' => 'y'])->keys()->collect(), ['a', 'b']);
    }

    public function testAssoc()
    {
        $it = iter(['a' => 'z', 'b' => 'y']);

        $this->assertEquals(['a' => 'z', 'b' => 'y'], $it->collect());

        $it = iter(['name' => 'Foo', 'test' => null]);
        $it = $it->filterEmpty();

        $this->assertEquals(['name' => 'Foo'], $it->collect());

        $it = iter(['name' => false, 'age' => 42]);
        $it = $it->filterEmpty();

        $this->assertEquals(['age' => 42], $it->collect());
    }

    public function testSlice()
    {
        $it = chars('FooBarQuatz')->slice(2, 4);

        $this->assertEquals('oBar', $it->implode());
    }

    public function testChunks()
    {
        $it = chars('FooBar')->chunks(2);

        $this->assertEquals([['F', 'o'], ['o', 'B'], ['a', 'r']], $it->collect());
    }

    public function testGroup()
    {
        $it = iter([1, 2, 4, 2, 3, 2, 4, 5, 1, 2, 4])->group();

        $this->assertEquals([[1, 1], [2, 2, 2, 2], [4, 4, 4], [3], [5]], $it->collect());

        $it = iter(['a' => 0, 'b' => 1, 'c' => 0])->groupKeepKeys();

        $this->assertEquals([['a' => 0, 'c' => 0], ['b' => 1]], $it->collect());
    }

    public function testExtract()
    {
        $records = [
            [
                'id'         => 2135,
                'first_name' => 'John',
                'last_name'  => 'Doe',
            ],
            [
                'id'         => 3245,
                'first_name' => 'Sally',
                'last_name'  => 'Smith',
            ],
            [
                'id'         => 5342,
                'first_name' => 'Jane',
                'last_name'  => 'Jones',
            ],
            [
                'id'         => 5623,
                'first_name' => 'Peter',
                'last_name'  => 'Doe',
            ]
        ];

        $it = iter($records);

        $this->assertEquals(['John', 'Sally', 'Jane', 'Peter'], $it->extractBykey('first_name')->collect());
        $this->assertEquals(
            [2135 => 'Doe', 3245 => 'Smith', 5342 => 'Jones', 5623 => 'Doe'],
            $it->extractBykey('last_name', 'id')->collect()
        );
    }

    public function testCountOccurences()
    {
        $it = iter([1, 'hello', 1, 'world', 'hello']);

        $this->assertEquals([1 => 2, 'hello' => 2, 'world' => 1], $it->countOccurrences());
    }

    public function testFold()
    {
        $it = iter([6, 7, 8]);

        $sum1 = function($sum, int $a) {
            return $sum + $a;
        };

        $sum2 = function(int $sum, int $a) {
            return $sum + $a;
        };

        $this->assertEquals(21, $it->fold($sum1));
        $this->assertEquals(63, $it->fold($sum2, 42));
    }

    public function testTakeWhile()
    {
        $it = iter([0, 1, 2, 10, 20]);

        $belowTen = function(int $item) {
            return $item < 10;
        };

        $this->assertEquals([0, 1, 2], $it->takeWhile($belowTen)->collect());
    }

    public function testSkipWhile()
    {
        $it = iter([0, 1, 2, 10, 20]);

        $belowTen = function(int $item) {
            return $item < 10;
        };

        $this->assertEquals([10, 20], $it->skipWhile($belowTen)->collect());
    }

    public function testBefore()
    {
        $it = chars("abcdef");
        $it = $it->before('c');

        $this->assertEquals('ab', $it->implode());
    }

    public function testBeforeAssoc()
    {
        $it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w']);
        $it = $it->before('x');

        $this->assertEquals(['a' => 'z', 'b' => 'y'], $it->collect());
    }

    public function testAfter()
    {
        $it = chars("abcdef");
        $it = $it->after('d');

        $this->assertEquals('ef', $it->implode());
    }

    public function testAfterAssoc()
    {
        $it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w']);
        $it = $it->after('x');

        $this->assertEquals(['d' => 'w'], $it->collect());
    }

    public function testFrom()
    {
        $it = chars("abcdef");
        $it = $it->from('d');

        $this->assertEquals('def', $it->implode());
    }

    public function testFromAssoc()
    {
        $it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w']);
        $it = $it->from('x');

        $this->assertEquals(['c' => 'x', 'd' => 'w'], $it->collect());
    }

    public function testUntil()
    {
        $it = chars("abcdef");
        $it = $it->until('c');

        $this->assertEquals('abc', $it->implode());
    }

    public function testUntilAssoc()
    {
        $it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w']);
        $it = $it->until('x');

        $this->assertEquals(['a' => 'z', 'b' => 'y', 'c' => 'x'], $it->collect());
    }

    public function testPeekNext()
    {
        $it = chars('Hal');

        $this->assertEquals('H', $it->current()->unwrap());
        $this->assertEquals('a', $it->peek()->unwrap());
        $this->assertEquals('H', $it->current()->unwrap());

        $this->assertEquals('H', $it->next()->unwrap());
        $this->assertEquals('a', $it->current()->unwrap());
        $this->assertEquals('l', $it->peek()->unwrap());
        $this->assertEquals('a', $it->current()->unwrap());
    }

    public function testConsume()
    {
        $it = chars('Hal')->consume();

        $this->assertEquals('H', $it->popFront()->unwrap());

        $this->assertEquals('a', $it->front()->unwrap());
        $this->assertEquals('a', $it->front()->unwrap());

        $this->assertEquals('l', $it->popBack()->unwrap());

        $this->assertEquals('a', $it->back()->unwrap());
        $this->assertEquals('a', $it->front()->unwrap());

        $this->assertEquals('a', $it->popFront()->unwrap());

        $this->assertTrue($it->front()->isNone());
        $this->assertTrue($it->back()->isNone());
        $this->assertTrue($it->isEmpty());
    }

    public function testAll()
    {
        $it1 = iter([0, 1, 2, 3]);
        $it2 = iter([-1, 2, 3, 4]);

        $positive = function(int $item) {
            return $item >= 0;
        };

        $this->assertTrue($it1->all($positive));
        $this->assertFalse($it2->all($positive));
    }

    public function testAny()
    {
        $it1 = iter([-1, 0, 1]);
        $it2 = iter([-1]);

        $positive = function(int $item) {
            return $item > 0;
        };

        $this->assertTrue($it1->any($positive));
        $this->assertFalse($it2->any($positive));
    }

    public function testSum()
    {
        $it = iter([1, 2, 3]);

        $this->assertEquals(6, $it->sum());
    }

    public function testProduct()
    {
        $it = iter([1, 2, 3, 4]);

        $this->assertEquals(24, $it->product());
    }

    public function testMax()
    {
        $it = iter([1, 2, 3, 4]);

        $this->assertEquals(4, $it->max());
    }

    public function testMin()
    {
        $it = iter([1, 2, 3, 4]);

        $this->assertEquals(1, $it->min());
    }

    public function testAccess()
    {
        $it = iter(['a' => 'foo', 'b' => 'bar']);

        $this->assertEquals('b', $it->firstKeyOf('bar')->unwrap());
        $this->assertTrue($it->find('bar')->isSome());
        $this->assertEquals('bar', $it->find('bar')->unwrap());
        $this->assertEquals('bar', $it->at('b')->unwrap());

        $it = chars('Foo');

        $this->assertEquals(1, $it->firstKeyOf('o')->unwrap());
        $this->assertTrue($it->find('F')->isSome());
        $this->assertEquals('F', $it->find('F')->unwrap());
        $this->assertEquals('o', $it->at(1)->unwrap());
    }

    public function testFind()
    {
        $it = iter(['a', 'b', 'c']);

        $result = $it->find('a');

        $this->assertTrue($result->isSome());
        $this->assertEquals('a', $result->unwrap());
        $this->assertTrue($it->find('z')->isNone());
    }

    public function testAllKeysOf()
    {
        $it = chars('Foo');

        $this->assertEquals([1, 2], $it->allKeysOf('o')->collect());
    }

    public function testKeyOf()
    {
        $it = iter(['a', 'b', 'c']);

        $result = $it->firstKeyOf('b');

        $this->assertTrue($result->isSome());
        $this->assertEquals(1, $result->unwrap());
        $this->assertTrue($it->firstKeyOf('z')->isNone());
    }

    public function testKeyOfAssoc()
    {
        $it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x']);

        $result = $it->firstKeyOf('y');

        $this->assertTrue($result->isSome());
        $this->assertEquals('b', $result->unwrap());
        $this->assertTrue($it->firstKeyOf('a')->isNone());
    }

    public function testAmount()
    {
        $this->assertEquals(11, iter(range(0, 10))->length());
        $this->assertEquals(10, chars('Hallo Welt')->length());
    }

    public function testAverage()
    {
        $it = iter(range(1, 8));

        $this->assertEquals(8, $it->length());
        $this->assertEquals(4.5, $it->average());
    }
}