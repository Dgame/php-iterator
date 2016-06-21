<?php

require_once '../vendor/autoload.php';

use function Dgame\Iterator\chain;
use function Dgame\Iterator\chars;
use function Dgame\Iterator\cycle;
use function Dgame\Iterator\iter;

class IteratorTest extends PHPUnit_Framework_TestCase
{
    public function testChars()
    {
        $it = chars('Hallo');

        $this->assertEquals($it->next()->get(), 'H');
        $this->assertEquals($it->next()->get(), 'a');
        $this->assertEquals($it->next()->get(), 'l');
        $this->assertEquals($it->next()->get(), 'l');
        $this->assertEquals($it->next()->get(), 'o');
        $this->assertTrue($it->next()->isNone());
    }

    public function testCollect()
    {
        $it = chars('Hallo');

        $this->assertEquals($it->collect(), ['H', 'a', 'l', 'l', 'o']);
        $this->assertEquals($it->implode(), 'Hallo');
    }

    public function testTake()
    {
        $it = chars('Hallo');
        $it = $it->take(2);

        $this->assertEquals($it->next()->get(), 'H');
        $this->assertEquals($it->next()->get(), 'a');
        $this->assertTrue($it->next()->isNone());
    }

    public function testSkip()
    {
        $it = chars('Hallo');
        $it = $it->skip(3);

        $this->assertEquals($it->next()->get(), 'l');
        $this->assertEquals($it->next()->get(), 'o');
        $this->assertTrue($it->next()->isNone());
    }

    public function testCycle()
    {
        $it = cycle([1, 2, 3])->take(2);

        $this->assertEquals($it->collect(), [1, 2, 3, 1, 2, 3]);
    }

    public function testChain()
    {
        $it = chain([1, 2, 3], ['a', 'b', 'c']);

        $this->assertEquals($it->collect(), [1, 2, 3, 'a', 'b', 'c']);
    }

    public function testChunks()
    {
        $it = chars('FooBar')->chunks(2);

        $this->assertEquals($it->collect(), [['F', 'o'], ['o', 'B'], ['a', 'r']]);
    }

    public function testGroup()
    {
        $it = iter([1, 2, 4, 2, 3, 2, 4, 5, 1, 2, 4])->group();

        $this->assertEquals($it->collect(), [[1, 1], [2, 2, 2, 2], [4, 4, 4], [3], [5]]);
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

        $this->assertEquals($it->fold($sum1), 21);
        $this->assertEquals($it->fold($sum2, 42), 63);
    }

    public function testTakeWhile()
    {
        $it = iter([0, 1, 2, 10, 20]);

        $belowTen = function(int $item) {
            return $item < 10;
        };

        $this->assertEquals($it->takeWhile($belowTen)->collect(), [0, 1, 2]);
    }

    public function testSkipWhile()
    {
        $it = iter([0, 1, 2, 10, 20]);

        $belowTen = function(int $item) {
            return $item < 10;
        };

        $this->assertEquals($it->skipWhile($belowTen)->collect(), [10, 20]);
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

        $this->assertEquals($it->sum(), 6);
    }

    public function testProduct()
    {
        $it = iter([1, 2, 3, 4]);

        $this->assertEquals($it->product(), 24);
    }

    public function testMax()
    {
        $it = iter([1, 2, 3, 4]);

        $this->assertEquals($it->max(), 4);
    }

    public function testMin()
    {
        $it = iter([1, 2, 3, 4]);

        $this->assertEquals($it->min(), 1);
    }

    public function testFind()
    {
        $it = iter(['a', 'b', 'c']);

        $result = $it->find('a');

        $this->assertTrue($result->isSome());
        $this->assertEquals($result->get(), 'a');
        $this->assertTrue($it->find('z')->isNone());
    }

    public function testIndexOf()
    {
        $it = iter(['a', 'b', 'c']);

        $result = $it->indexOf('b');

        $this->assertTrue($result->isSome());
        $this->assertEquals($result->get(), 1);
        $this->assertTrue($it->indexOf('z')->isNone());
    }
}