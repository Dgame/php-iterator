<?php

require_once '../vendor/autoload.php';

use Dgame\Iterator\Optional\NullObject;
use PHPUnit\Framework\TestCase;
use function Dgame\Iterator\Optional\maybe;
use function Dgame\Iterator\Optional\none;
use function Dgame\Iterator\Optional\some;

class TestOptional extends TestCase
{
    public function testSome()
    {
        $some = some(42);
        $this->assertTrue($some->isSome());
        $this->assertEquals(42, $some->get());
    }

    public function testNone()
    {
        $none = none();
        $this->assertTrue($none->isNone());
    }

    public function testMaybe()
    {
        $maybe = maybe(null);
        $this->assertTrue($maybe->isNone());

        $maybe = maybe(42);
        $this->assertTrue($maybe->isSome());
        $this->assertEquals(42, $maybe->get());
    }

    public function testChain()
    {
        $a = new class
        {
            public function test() : int
            {
                return 42;
            }
        };

        $some = some($a);
        $this->assertEquals(42, $some->get()->test());
        $this->assertEquals(42, $some->assume()->test());

        $none = none();
        $this->assertSame(NullObject::Instance(), $none->assume()->test());
    }
}