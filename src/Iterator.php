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
    private $iteration = 0;
    /**
     * @var null
     */
    private $length = null;

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
     * @return array
     */
    public function collect() : array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function implode(string $glue = '') : string
    {
        return implode($glue, $this->data);
    }

    /**
     * @return Consume
     */
    public function consume() : Consume
    {
        return new Consume($this);
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function map(callable $callback) : Iterator
    {
        return new self(array_map($callback, $this->data));
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function filter(callable $callback) : Iterator
    {
        return new self(array_filter($this->data, $callback));
    }

    /**
     * @return Iterator
     */
    public function filterEmpty() : Iterator
    {
        return new self(array_filter($this->data));
    }

    /**
     * @return Iterator
     */
    public function group() : Iterator
    {
        $result = [];
        foreach ($this->data as $value) {
            $result[$value][] = $value;
        }

        return iter($result);
    }

    /**
     * @return Iterator
     */
    public function unique() : Iterator
    {
        return new self(array_unique($this->data));
    }

    /**
     * @param int $n
     *
     * @return Iterator
     */
    public function take(int $n) : Iterator
    {
        return new self(array_slice($this->data, 0, $n));
    }

    /**
     * @param int $n
     *
     * @return Iterator
     */
    public function skip(int $n) : Iterator
    {
        return new self(array_slice($this->data, $n));
    }

    /**
     * @param int $size
     *
     * @return Iterator
     */
    public function chunks(int $size) : Iterator
    {
        $chunks = array_chunk($this->data, $size);

        return new self($chunks);
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function takeWhile(callable $callback) : Iterator
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
    public function skipWhile(callable $callback) : Iterator
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
     * @param $value
     *
     * @return Iterator
     */
    public function before($value) : Iterator
    {
        $index = $this->indexOf($value);
        if ($index->isSome()) {
            return $this->take($index->get());
        }

        return new self([]);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function after($value) : Iterator
    {
        $index = $this->indexOf($value);
        if ($index->isSome()) {
            return $this->skip($index->get() + 1);
        }

        return new self([]);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function from($value) : Iterator
    {
        $index = $this->indexOf($value);
        if ($index->isSome()) {
            return $this->skip($index->get());
        }

        return new self([]);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function until($value) : Iterator
    {
        $index = $this->indexOf($value);
        if ($index->isSome()) {
            return $this->take($index->get() + 1);
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
     * @param callable $callback
     *
     * @return bool
     */
    public function all(callable $callback) : bool
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
    public function any(callable $callback) : bool
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
    public function sum() : float
    {
        return array_sum($this->data);
    }

    /**
     * @return float
     */
    public function product() : float
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
    public function length() : int
    {
        if ($this->length === null) {
            $this->length = count($this->data);
        }

        return $this->length;
    }

    /**
     * @return float
     */
    public function average() : float
    {
        return $this->sum() / $this->length();
    }

    /**
     * @return Iterator
     */
    public function reverse() : Iterator
    {
        return new self(array_reverse($this->data));
    }

    /**
     * @return Optional
     */
    public function current() : Optional
    {
        if ($this->isValid()) {
            return maybe($this->data[$this->iteration]);
        }

        return none();
    }

    /**
     * @return Optional
     */
    public function peek() : Optional
    {
        if ($this->hasNext()) {
            $index = $this->iteration + 1;
            $value = $this->data[$index];

            return maybe($value);
        }

        return none();
    }

    /**
     * @return bool
     */
    public function hasNext() : bool
    {
        return ($this->iteration + 1) < $this->length();
    }

    /**
     * @return Optional
     */
    public function next() : Optional
    {
        $result = $this->current();
        if ($this->isValid()) {
            $this->iteration++;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->data);
    }

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return $this->iteration < $this->length();
    }

    /**
     *
     */
    public function reset()
    {
        $this->iteration = 0;
    }

    /**
     * @param $value
     *
     * @return Optional
     */
    public function find($value) : Optional
    {
        $index = $this->indexOf($value);
        if ($index->isSome()) {
            return maybe($this->data[$index->get()]);
        }

        return none();
    }

    /**
     * @param $value
     *
     * @return Optional
     */
    public function indexOf($value) : Optional
    {
        $index = array_search($value, $this->data);
        if ($index === false) {
            return none();
        }

        return some($index);
    }
}

/**
 * @param array[] ...$args
 *
 * @return Iterator
 */
function chain(array ...$args) : Iterator
{
    $data = [];
    foreach ($args as $arg) {
        array_push($data, ...$arg);
    }

    return new Iterator($data);
}

/**
 * @param array $data
 *
 * @return Iterator
 */
function iter(array $data) : Iterator
{
    return new Iterator(array_values($data));
}

/**
 * @param string $str
 *
 * @return Iterator
 */
function chars(string $str) : Iterator
{
    return new Iterator(str_split($str));
}

/**
 * @param array $data
 *
 * @return Iterator
 */
function keys(array $data) : Iterator
{
    return new Iterator(array_keys($data));
}