<?php

namespace Dgame\Iterator;

use Dgame\Iterator\Optional\Optional;
use function Dgame\Iterator\Optional\maybe;
use function Dgame\Iterator\Optional\none;
use function Dgame\Iterator\Optional\some;

/**
 * Class Iterator
 * @package Dgame\Iterator
 */
final class Iterator
{
    /**
     * @var array
     */
    private $data = [];
    /**
     * @var int
     */
    private $index = 0;

    /**
     * Iterator constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        return $this->data;
    }

    /**
     * @param string|null $glue
     *
     * @return string
     */
    public function implode(string $glue = null): string
    {
        if ($glue === null) {
            return implode($this->data);
        }

        return implode($glue, $this->data);
    }

    /**
     * @return Consume
     */
    public function consume(): Consume
    {
        return new Consume($this);
    }

    /**
     * @return Iterator
     */
    public function values(): Iterator
    {
        return new self(array_values($this->data));
    }

    /**
     * @return Iterator
     */
    public function keys(): Iterator
    {
        return new self(array_keys($this->data));
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function map(callable $callback): Iterator
    {
        return new self(array_map($callback, $this->data));
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function filter(callable $callback): Iterator
    {
        return new self(array_filter($this->data, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @return Iterator
     */
    public function filterEmpty(): Iterator
    {
        return new self(array_filter($this->data));
    }

    /**
     * @return Iterator
     */
    public function group(): Iterator
    {
        $result = [];
        foreach ($this->data as $key => $value) {
            $result[$value][] = $value;
        }

        return new self(array_values($result));
    }

    /**
     * @return Iterator
     */
    public function groupKeepKeys(): Iterator
    {
        $result = [];
        foreach ($this->data as $key => $value) {
            $result[$value][$key] = $value;
        }

        return new self(array_values($result));
    }

    /**
     * @param      $column_key
     * @param null $index_key
     *
     * @return Iterator
     */
    public function extractByKey($column_key, $index_key = null): Iterator
    {
        return new self(array_column($this->data, $column_key, $index_key));
    }

    /**
     * @return Iterator
     */
    public function unique(): Iterator
    {
        return new self(array_unique($this->data));
    }

    /**
     * @param int $n
     *
     * @return Iterator
     */
    public function take(int $n): Iterator
    {
        return new self(array_slice($this->data, 0, $n));
    }

    /**
     * @param int $n
     *
     * @return Iterator
     */
    public function skip(int $n): Iterator
    {
        return new self(array_slice($this->data, $n));
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return Iterator
     */
    public function slice(int $offset, int $length): Iterator
    {
        return new self(array_slice($this->data, $offset, $length));
    }

    /**
     * @param int $size
     *
     * @return Iterator
     */
    public function chunks(int $size): Iterator
    {
        $chunks = array_chunk($this->data, $size);

        return new self($chunks);
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function takeWhile(callable $callback): Iterator
    {
        $n = 0;
        foreach ($this->data as $value) {
            if (!$callback($value)) {
                break;
            }

            $n++;
        }

        return $this->take($n);
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function skipWhile(callable $callback): Iterator
    {
        $n = 0;
        foreach ($this->data as $value) {
            if (!$callback($value)) {
                break;
            }

            $n++;
        }

        return $this->skip($n);
    }

    /**
     * @param $left
     * @param $right
     *
     * @return Iterator
     */
    public function between($left, $right): Iterator
    {
        if ($this->firstIndexOf($left)->isSome($offset)) {
            if ($this->firstIndexOf($right)->isSome($range)) {
                return $this->slice($offset + 1, $range - $offset - 1);
            }

            return $this->skip($offset + 1);
        }

        return new self([]);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function before($value): Iterator
    {
        if ($this->firstIndexOf($value)->isSome($n)) {
            return $this->take($n);
        }

        return new self([]);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function after($value): Iterator
    {
        if ($this->firstIndexOf($value)->isSome($n)) {
            return $this->skip($n + 1);
        }

        return new self([]);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function from($value): Iterator
    {
        if ($this->firstIndexOf($value)->isSome($n)) {
            return $this->skip($n);
        }

        return new self([]);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function until($value): Iterator
    {
        if ($this->firstIndexOf($value)->isSome($n)) {
            return $this->take($n + 1);
        }

        return new self([]);
    }

    /**
     * @param callable $callback
     * @param null     $initial
     *
     * @return mixed
     */
    public function fold(callable $callback, $initial = null)
    {
        return array_reduce($this->data, $callback, $initial);
    }

    /**
     * @param $key
     *
     * @return Optional
     */
    public function at($key): Optional
    {
        if (array_key_exists($key, $this->data)) {
            return maybe($this->data[$key]);
        }

        return none();
    }

    /**
     * @param $value
     *
     * @return Optional
     */
    public function find($value): Optional
    {
        if ($this->firstKeyOf($value)->isSome($key)) {
            return maybe($this->data[$key]);
        }

        return none();
    }

    /**
     * @param callable $callback
     *
     * @return array
     */
    public function findBy(callable $callback): array
    {
        $results = [];
        foreach ($this->data as $key => $value) {
            if ($callback($value, $key)) {
                $results[$key] = $value;
            }
        }

        return $results;
    }

    /**
     * @param $value
     *
     * @return Optional
     */
    public function firstIndexOf($value): Optional
    {
        return $this->values()->firstKeyOf($value);
    }

    /**
     * @param $value
     *
     * @return Optional
     */
    public function firstKeyOf($value): Optional
    {
        $key = array_search($value, $this->data);
        if ($key === false) {
            return none();
        }

        return some($key);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function allIndicesOf($value): Iterator
    {
        return $this->values()->allKeysOf($value);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function allKeysOf($value): Iterator
    {
        return new self(array_keys($this->data, $value));
    }

    /**
     * @param callable $callback
     *
     * @return bool
     */
    public function all(callable $callback): bool
    {
        foreach ($this->data as $value) {
            if (!$callback($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param callable $callback
     *
     * @return bool
     */
    public function any(callable $callback): bool
    {
        foreach ($this->data as $value) {
            if ($callback($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return float
     */
    public function sum(): float
    {
        return array_sum($this->data);
    }

    /**
     * @return float
     */
    public function product(): float
    {
        return array_product($this->data);
    }

    /**
     * @return mixed
     */
    public function max()
    {
        return max($this->data);
    }

    /**
     * @return mixed
     */
    public function min()
    {
        return min($this->data);
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return count($this->data);
    }

    /**
     * @return array
     */
    public function countOccurrences(): array
    {
        return array_count_values($this->data);
    }

    /**
     * @return float
     */
    public function average(): float
    {
        return $this->sum() / $this->length();
    }

    /**
     * @return Iterator
     */
    public function reverse(): Iterator
    {
        return new self(array_reverse($this->data));
    }

    /**
     * @return Optional
     */
    public function key(): Optional
    {
        return maybe(key($this->data));
    }

    /**
     * @return Optional
     */
    public function current(): Optional
    {
        if ($this->isValid()) {
            return maybe(current($this->data));
        }

        return none();
    }

    /**
     * @return Optional
     */
    public function previous(): Optional
    {
        if ($this->hasPrevious()) {
            $this->index--;

            return maybe(prev($this->data));
        }

        return none();
    }

    /**
     * @return Optional
     */
    public function begin(): Optional
    {
        $this->index = 0;
        if ($this->isEmpty()) {
            return none();
        }

        return maybe(reset($this->data));
    }

    /**
     * @return Optional
     */
    public function end(): Optional
    {
        $this->index = $this->length() - 1;
        if ($this->isEmpty()) {
            return none();
        }

        return maybe(end($this->data));
    }

    /**
     * @return Optional
     */
    public function peek(): Optional
    {
        next($this->data);
        if (key($this->data) !== null) {
            try {
                return $this->current();
            } finally {
                prev($this->data);
            }
        }

        return none();
    }

    /**
     * @return Optional
     */
    public function next(): Optional
    {
        try {
            return $this->current();
        } finally {
            if ($this->isValid()) {
                $this->index++;
                next($this->data);
            }
        }
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->index < $this->length();
    }

    /**
     * @return bool
     */
    public function hasNext(): bool
    {
        return ($this->index + 1) < $this->length();
    }

    /**
     * @return bool
     */
    public function hasPrevious(): bool
    {
        return $this->index > 0;
    }
}
