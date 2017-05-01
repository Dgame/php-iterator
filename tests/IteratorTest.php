<?php

use PHPUnit\Framework\TestCase;
use function Dgame\Iterator\chars;
use function Dgame\Iterator\iter;
use function Dgame\Iterator\only;

class IteratorTest extends TestCase
{
    public function testFilterEmpty()
    {
        $it = iter(['a', 'b', false, null, 0, 1])->filter();

        $this->assertEquals('b', $it->next());
        $this->assertEquals(1, $it->next());
    }

    public function testOnly()
    {
        $it = only('a')->repeat(4);

        $this->assertEquals('aaaa', $it->implode());
    }

    public function testChars()
    {
        $it = chars('Hallo');

        $this->assertEquals('H', $it->first());
        $this->assertEquals('a', $it->next());
        $this->assertEquals('l', $it->next());
        $this->assertEquals('l', $it->next());
        $this->assertEquals('o', $it->next());
    }

    public function testExplodeChars()
    {
        $it = chars(parent::class, '\\');

        $this->assertEquals('PHPUnit\Framework\TestCase', $it->implode('\\'));
        $this->assertEquals('PHPUnit', $it->first());
        $this->assertEquals('Framework', $it->next());
        $this->assertEquals('TestCase', $it->next());
    }

    public function testFirst()
    {
        $this->assertEquals('PHPUnit', chars(parent::class, '\\')->first());
    }

    public function testLast()
    {
        $this->assertEquals('TestCase', chars(parent::class, '\\')->last());
    }

    public function testPopFront()
    {
        $this->assertEquals('PHPUnit', chars(parent::class, '\\')->popFront());
    }

    public function testPopBack()
    {
        $this->assertEquals('TestCase', chars(parent::class, '\\')->popBack());
    }

    public function testCollect()
    {
        $this->assertEquals(['H', 'a', 'l', 'l', 'o'], chars('Hallo')->collect());
    }

    public function testImplode()
    {
        $this->assertEquals('Hallo', chars('Hallo')->implode());
    }

    public function testTake()
    {
        $this->assertEquals('Hal', chars('Hallo')->take(3)->implode());
    }

    public function testSkip()
    {
        $this->assertEquals('lo', chars('Hallo')->skip(3)->implode());
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
        $this->assertEquals(['a' => 'z', 'b' => 'y'], iter(['a' => 'z', 'b' => 'y'])->collect());
        $this->assertEquals(['name' => 'Foo'], iter(['name' => 'Foo', 'test' => null])->filter()->collect());
        $this->assertEquals(['age' => 42], iter(['name' => false, 'age' => 42])->filter()->collect());
    }

    public function testSlice()
    {
        $this->assertEquals('oBar', chars('FooBarQuatz')->slice(2, 6)->implode());
    }

    public function testChunks()
    {
        $this->assertEquals([['F', 'o'], ['o', 'B'], ['a', 'r']], chars('FooBar')->chunks(2)->collect());
    }

    public function testFold()
    {
        $it = iter([6, 7, 8]);

        $sum1 = function ($sum, int $a) {
            return $sum + $a;
        };

        $sum2 = function (int $sum, int $a) {
            return $sum + $a;
        };

        $this->assertEquals(21, $it->fold($sum1));
        $this->assertEquals(63, $it->fold($sum2, 42));
    }

    public function testTakeWhile()
    {
        $belowTen = function (int $item) {
            return $item < 10;
        };

        $this->assertEquals([0, 1, 2], iter([0, 1, 2, 10, 20])->takeWhile($belowTen)->collect());
    }

    public function testSkipWhile()
    {
        $belowTen = function (int $item) {
            return $item < 10;
        };

        $this->assertEquals([10, 20], iter([0, 1, 2, 10, 20])->skipWhile($belowTen)->collect());
    }

    public function testBefore()
    {
        $this->assertEquals('ab', chars('abcdef')->before('c')->implode());
    }

    public function testBeforeAssoc()
    {
        $this->assertEquals(['a' => 'z', 'b' => 'y'], iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w'])->before('x')->collect());
    }

    public function testAfter()
    {
        $this->assertEquals('ef', chars('abcdef')->after('d')->implode());
    }

    public function testAfterAssoc()
    {
        $this->assertEquals(['d' => 'w'], iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w'])->after('x')->collect());
    }

    public function testFrom()
    {
        $this->assertEquals('def', chars('abcdef')->from('d')->implode());
    }

    public function testFromAssoc()
    {
        $this->assertEquals(['c' => 'x', 'd' => 'w'], iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w'])->from('x')->collect());
    }

    public function testUntil()
    {
        $this->assertEquals('abc', chars('abcdef')->until('c')->implode());
    }

    public function testUntilAssoc()
    {
        $this->assertEquals(['a' => 'z', 'b' => 'y', 'c' => 'x'], iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w'])->until('x')->collect());
    }

    public function testAll()
    {
        $positive = function (int $item) {
            return $item >= 0;
        };

        $this->assertTrue(iter([0, 1, 2, 3])->all($positive));
        $this->assertFalse(iter([-1, 2, 3, 4])->all($positive));
    }

    public function testAny()
    {
        $positive = function (int $item) {
            return $item > 0;
        };

        $this->assertTrue(iter([-1, 0, 1])->any($positive));
        $this->assertFalse(iter([-1])->any($positive));
    }

    public function testFind()
    {
        $this->assertEquals([0], iter(['a', 'b', 'c'])->keysOf('a')->collect());
        $this->assertEquals([1, 2], chars('fooBar')->keysOf('o')->collect());
    }

    public function testKeyOf()
    {
        $it = iter(['a', 'b', 'c']);

        $this->assertEquals(1, $it->keyOf('b'));
        $this->assertFalse($it->keyOf('z'));
    }

    public function testKeyOfAssoc()
    {
        $it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x']);

        $this->assertEquals('b', $it->keyOf('y'));
        $this->assertFalse($it->keyOf('a'));
    }

    public function testAmount()
    {
        $this->assertEquals(11, iter(range(0, 10))->length());
        $this->assertEquals(10, chars('Hallo Welt')->length());
    }
}