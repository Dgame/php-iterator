<?php

require_once '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use function Dgame\Iterator\assoc;
use function Dgame\Iterator\chain;
use function Dgame\Iterator\chars;
use function Dgame\Iterator\cycle;
use function Dgame\Iterator\iter;
use function Dgame\Iterator\keys;

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

    public function testChars()
    {
        $it = chars('Hallo');

        $this->assertEquals('H', $it->current()->get());
        $this->assertEquals('H', $it->next()->get());
        $this->assertEquals('a', $it->next()->get());
        $this->assertEquals('l', $it->next()->get());
        $this->assertEquals('l', $it->next()->get());
        $this->assertEquals('o', $it->next()->get());
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

        $this->assertEquals('H', $it->next()->get());
        $this->assertEquals('a', $it->next()->get());
        $this->assertTrue($it->next()->isNone());
    }

    public function testSkip()
    {
        $it = chars('Hallo');
        $it = $it->skip(3);

        $this->assertEquals('l', $it->next()->get());
        $this->assertEquals('o', $it->next()->get());
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

    public function testAssocIter()
    {
        $it = iter(['a' => 'z', 'b' => 'y']);

        $this->assertEquals('z', $it->next()->get());
        $this->assertEquals('y', $it->next()->get());
        $this->assertTrue($it->next()->isNone());
    }

    public function testAssocKeys()
    {
        $it = keys(['a' => 'z', 'b' => 'y']);

        $this->assertEquals('a', $it->next()->get());
        $this->assertEquals('b', $it->next()->get());
        $this->assertTrue($it->next()->isNone());
    }

    public function testAssoc()
    {
        $it = assoc(['a' => 'z', 'b' => 'y']);

        $this->assertEquals('a', $it->getKeys()->next()->get());
        $this->assertEquals('b', $it->getKeys()->next()->get());
        $this->assertTrue($it->getKeys()->next()->isNone());

        $this->assertEquals('z', $it->getValues()->next()->get());
        $this->assertEquals('y', $it->getValues()->next()->get());
        $this->assertTrue($it->getValues()->next()->isNone());

        $this->assertEquals(['a' => 'z', 'b' => 'y'], $it->combine());
    }

    public function testAssocAlignment()
    {
        $it = assoc(['name' => 'Foo', 'test' => null]);
        $iv = $it->getValues()->filterEmpty();
        $it->setValues($iv);

        $this->assertEquals(['name' => 'Foo'], $it->combine());

        $it = assoc(['name' => false, 'age' => 42]);
        $iv = $it->getValues()->filterEmpty();
        $it->setValues($iv);

        $this->assertEquals(['age' => 42], $it->combine());

        $it = assoc(['abc' => 'test', 'a' => 'foobar']);
        $ik = $it->getKeys()->filter(function(string $key) {
            return strlen($key) > 1;
        });
        $it->setKeys($ik);

        $this->assertEquals(['abc' => 'test'], $it->combine());
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

    public function testAfter()
    {
        $it = chars("abcdef");
        $it = $it->after('d');

        $this->assertEquals('ef', $it->implode());
    }

    public function testFrom()
    {
        $it = chars("abcdef");
        $it = $it->from('d');

        $this->assertEquals('def', $it->implode());
    }

    public function testUntil()
    {
        $it = chars("abcdef");
        $it = $it->until('c');

        $this->assertEquals('abc', $it->implode());
    }

    public function testPeekNext()
    {
        $it = chars('Hal');

        $this->assertEquals('H', $it->current()->get());
        $this->assertEquals('a', $it->peek()->get());
        $this->assertEquals('H', $it->current()->get());

        $this->assertTrue($it->hasNext());
        $this->assertEquals('H', $it->next()->get());
        $this->assertTrue($it->hasNext());

        $this->assertEquals('a', $it->current()->get());
        $this->assertEquals('l', $it->peek()->get());
        $this->assertEquals('a', $it->current()->get());

        $this->assertTrue($it->hasNext());
    }

    public function testConsume()
    {
        $it = chars('Hal')->consume();

        $this->assertEquals('H', $it->popFront()->get());

        $this->assertEquals('a', $it->front()->get());
        $this->assertEquals('a', $it->front()->get());

        $this->assertEquals('l', $it->popBack()->get());

        $this->assertEquals('a', $it->back()->get());
        $this->assertEquals('a', $it->front()->get());

        $this->assertEquals('a', $it->popFront()->get());

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

    public function testFind()
    {
        $it = iter(['a', 'b', 'c']);

        $result = $it->find('a');

        $this->assertTrue($result->isSome());
        $this->assertEquals('a', $result->get());
        $this->assertTrue($it->find('z')->isNone());
    }

    public function testIndexOf()
    {
        $it = iter(['a', 'b', 'c']);

        $result = $it->indexOf('b');

        $this->assertTrue($result->isSome());
        $this->assertEquals(1, $result->get());
        $this->assertTrue($it->indexOf('z')->isNone());
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