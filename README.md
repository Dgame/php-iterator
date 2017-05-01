# php-iterator

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Dgame/php-iterator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Dgame/php-iterator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Dgame/php-iterator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Dgame/php-iterator/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/Dgame/php-iterator/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Dgame/php-iterator/build-status/master)
[![StyleCI](https://styleci.io/repos/61667694/shield?branch=master)](https://styleci.io/repos/61667694)
[![Build Status](https://travis-ci.org/Dgame/php-iterator.svg?branch=master)](https://travis-ci.org/Dgame/php-iterator)

## Enjoy high order functions

### only & repeat
```php
$this->assertEquals('aaaa', only('a')->repeat(4)->implode());
```

### take
```php
$this->assertEquals('Hal', chars('Hallo')->take(3)->implode());
```

### skip
```php
$this->assertEquals('lo', chars('Hallo')->skip(3)->implode());
```

### slice
```php
$this->assertEquals('oBar', chars('FooBarQuatz')->slice(2, 6)->implode());
```

### chunks
```php
$this->assertEquals([['F', 'o'], ['o', 'B'], ['a', 'r']], chars('FooBar')->chunks(2)->collect());
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
$belowTen = function (int $item) {
    return $item < 10;
};

$this->assertEquals([0, 1, 2], iter([0, 1, 2, 10, 20])->takeWhile($belowTen)->collect());
```

### skip while
```php
$belowTen = function (int $item) {
    return $item < 10;
};

$this->assertEquals([10, 20], iter([0, 1, 2, 10, 20])->skipWhile($belowTen)->collect());
```

### before
```php
$this->assertEquals('ab', chars('abcdef')->before('c')->implode());
```

```php
$this->assertEquals(['a' => 'z', 'b' => 'y'], iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w'])->before('x')->collect());
```

### after
```php
$this->assertEquals('ef', chars('abcdef')->after('d')->implode());
```

```php
$this->assertEquals(['d' => 'w'], iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w'])->after('x')->collect());
```

### from
```php
$this->assertEquals('def', chars('abcdef')->from('d')->implode());
```

```php
$this->assertEquals(['c' => 'x', 'd' => 'w'], iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w'])->from('x')->collect());
```

### until
```php
$this->assertEquals('abc', chars('abcdef')->until('c')->implode());
```

```php
$this->assertEquals(['a' => 'z', 'b' => 'y', 'c' => 'x'], iter(['a' => 'z', 'b' => 'y', 'c' => 'x', 'd' => 'w'])->until('x')->collect());
```

### all
```php
$positive = function (int $item) {
    return $item >= 0;
};

$this->assertTrue(iter([0, 1, 2, 3])->all($positive));
$this->assertFalse(iter([-1, 2, 3, 4])->all($positive));
```

### any
```php
$positive = function (int $item) {
    return $item > 0;
};

$this->assertTrue(iter([-1, 0, 1])->any($positive));
$this->assertFalse(iter([-1])->any($positive));
```
