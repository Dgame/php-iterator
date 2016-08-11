# php-iterator

## Enjoy high order functions

### only & repeat
```php
$it = only('a')->repeat(4);

$this->assertEquals('aaaa', $it->implode());
```

### take
```php
$it = chars('Hallo');
$it = $it->take(2);

$this->assertEquals('H', $it->next()->unwrap());
$this->assertEquals('a', $it->next()->unwrap());
$this->assertTrue($it->next()->isNone());
```

### skip
```php
$it = chars('Hallo');
$it = $it->skip(3);

$this->assertEquals('l', $it->next()->unwrap());
$this->assertEquals('o', $it->next()->unwrap());
$this->assertTrue($it->next()->isNone());
```

### cycle
```php
$it = cycle([1, 2, 3])->take(2);

$this->assertEquals([1, 2, 3, 1, 2, 3], $it->collect());
```

### chain
```php
$it = chain([1, 2, 3], ['a', 'b', 'c']);

$this->assertEquals([1, 2, 3, 'a', 'b', 'c'], $it->collect());
```

### slice
```php
$it = chars('FooBarQuatz')->slice(2, 4);

$this->assertEquals('oBar', $it->implode());
```

### chunks
```php
$it = chars('FooBar')->chunks(2);

$this->assertEquals([['F', 'o'], ['o', 'B'], ['a', 'r']], $it->collect());
```

### group
```php
$it = iter([1, 2, 4, 2, 3, 2, 4, 5, 1, 2, 4])->group();

$this->assertEquals([[1, 1], [2, 2, 2, 2], [4, 4, 4], [3], [5]], $it->collect());
```

```php
$it = iter(['a' => 0, 'b' => 1, 'c' => 0])->groupKeepKeys();

$this->assertEquals([['a' => 0, 'c' => 0], ['b' => 1]], $it->collect());
```

### fold
```php
$it = iter([6, 7, 8]);

$sum1 = function($sum, int $a) {
    return $sum + $a;
};

$sum2 = function(int $sum, int $a) {
    return $sum + $a;
};

$this->assertEquals(21, $it->fold($sum1));
$this->assertEquals(63, $it->fold($sum2, 42));
```

### take while
```php
$it = iter([0, 1, 2, 10, 20]);
$belowTen = function(int $item) {
    return $item < 10;
};

$this->assertEquals([0, 1, 2], $it->takeWhile($belowTen)->collect());
```

### skip while
```php
$it = iter([0, 1, 2, 10, 20]);
$belowTen = function(int $item) {
    return $item < 10;
};

$this->assertEquals([10, 20], $it->skipWhile($belowTen)->collect());
```

### between
```php
$it = iter([0, 1, 2, 10, 20]);
$this->assertEquals([2, 10, 20], $it->between(1, 30)->collect());
```

```php
$it = iter(['a' => 'b', 'c' => 'd']);
$this->assertEquals(['c' => 'd'], $it->between('b', 'f')->collect());
```

```php
$it = chars('FooBa');
$this->assertEquals('ooB', $it->between('F', 'a')->implode());

$it = chars('Hallo');
$this->assertEmpty($it->between('f', 'x')->implode());
```

### before
```php
$it = chars('abcdef');
$it = $it->before('c');

$this->assertEquals('ab', $it->implode());
```

```php
$it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w']);
$it = $it->before('x');

$this->assertEquals(['a' => 'z', 'b' => 'y'], $it->collect());
```

### after
```php
$it = chars('abcdef');
$it = $it->after('d');

$this->assertEquals('ef', $it->implode());
```

```php
$it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w']);
$it = $it->after('x');

$this->assertEquals(['d' => 'w'], $it->collect());
```

### from
```php
$it = chars('abcdef');
$it = $it->from('d');

$this->assertEquals('def', $it->implode());
```

```php
$it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w']);
$it = $it->from('x');

$this->assertEquals(['c' => 'x', 'd' => 'w'], $it->collect());
```

### until
```php
$it = chars('abcdef');
$it = $it->until('c');

$this->assertEquals('abc', $it->implode());
```

```php
$it = iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w']);
$it = $it->until('x');

$this->assertEquals(['a' => 'z', 'b' => 'y', 'c' => 'x'], $it->collect());
```

### consume
```php
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
```

### all
```php
$it1 = iter([0, 1, 2, 3]);
$it2 = iter([-1, 2, 3, 4]);

$positive = function(int $item) {
    return $item >= 0;
};

$this->assertTrue($it1->all($positive));
$this->assertFalse($it2->all($positive));
```

### any
```php
$it1 = iter([-1, 0, 1]);
$it2 = iter([-1]);

$positive = function(int $item) {
    return $item > 0;
};

$this->assertTrue($it1->any($positive));
$this->assertFalse($it2->any($positive));
```

### find
```php
$it = iter(['a', 'b', 'c']);
$result = $it->find('a');

$this->assertTrue($result->isSome());
$this->assertEquals('a', $result->unwrap());
$this->assertTrue($it->find('z')->isNone());
```
