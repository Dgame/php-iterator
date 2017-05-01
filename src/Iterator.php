<?php

namespace Dgame\Iterator;

/**
 * Class Iterator
 * @package Dgame\Iterator
 */
final class Iterator
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * Iterator constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @param string $glue
     *
     * @return string
     */
    public function implode(string $glue = ''): string
    {
        return implode($glue, $this->values);
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        return $this->values;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->values);
    }

    /**
     * @return int
     */
    public function length(): int
    {
        return count($this->values);
    }

    /**
     * @return Iterator
     */
    public function values(): self
    {
        return new self(array_values($this->values));
    }

    /**
     * @return Iterator
     */
    public function keys(): self
    {
        return new self(array_keys($this->values));
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return reset($this->values);
    }

    /**
     * @return mixed
     */
    public function last()
    {
        return end($this->values);
    }

    /**
     * @return mixed
     */
    public function next()
    {
        return next($this->values);
    }

    /**
     * @return mixed
     */
    public function previous()
    {
        return prev($this->values);
    }

    /**
     * @return mixed
     */
    public function peek()
    {
        $value = $this->next();
        $this->previous();

        return $value;
    }

    /**
     * @return mixed
     */
    public function popBack()
    {
        return array_pop($this->values);
    }

    /**
     * @return mixed
     */
    public function popFront()
    {
        return array_shift($this->values);
    }

    /**
     * @param int $amount
     *
     * @return Iterator
     */
    public function take(int $amount): self
    {
        return new self(array_slice($this->values, 0, $amount));
    }

    /**
     * @param int $amount
     *
     * @return Iterator
     */
    public function skip(int $amount): self
    {
        return new self(array_slice($this->values, $amount));
    }

    /**
     * @param int $times
     *
     * @return Iterator
     */
    public function repeat(int $times): self
    {
        $values = [];
        for ($i = 0; $i < $times; $i++) {
            $values = array_merge($values, $this->values);
        }

        return new self($values);
    }

    /**
     * @param int $from
     * @param int $too
     *
     * @return Iterator
     */
    public function slice(int $from, int $too): self
    {
        return new self(array_slice($this->values, $from, $too - $from));
    }

    /**
     * @param int $size
     *
     * @return Iterator
     */
    public function chunks(int $size): self
    {
        return new self(array_chunk($this->values, $size));
    }

    /**
     * @param callable $callback
     * @param null     $initial
     *
     * @return mixed
     */
    public function fold(callable $callback, $initial = null)
    {
        return array_reduce($this->values, $callback, $initial);
    }

    /**
     * @param callable|null $callback
     *
     * @return Iterator
     */
    public function filter(callable $callback = null): self
    {
        if ($callback === null) {
            return new self(array_filter($this->values));
        }

        return new self(array_filter($this->values, $callback));
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->values));
    }

    /**
     * @return Iterator
     */
    public function unique(): self
    {
        return new self(array_unique($this->values));
    }

    /**
     * @return Iterator
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->values));
    }

    /**
     * @param callable $callback
     *
     * @return int
     */
    private function countWhile(callable $callback): int
    {
        $index = 0;
        foreach ($this->values as $key => $value) {
            if (!$callback($value, $key)) {
                break;
            }

            $index++;
        }

        return $index;
    }

    /**
     * @param callable $callback
     *
     * @return int
     */
    private function countUntil(callable $callback): int
    {
        $index = 0;
        foreach ($this->values as $key => $value) {
            if ($callback($value, $key)) {
                break;
            }

            $index++;
        }

        return $index;
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function takeWhile(callable $callback): self
    {
        return $this->take($this->countWhile($callback));
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function takeUntil(callable $callback): self
    {
        return $this->take($this->countUntil($callback));
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function skipWhile(callable $callback): self
    {
        return $this->skip($this->countWhile($callback));
    }

    /**
     * @param callable $callback
     *
     * @return Iterator
     */
    public function skipUntil(callable $callback): self
    {
        return $this->skip($this->countUntil($callback));
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function from($value): self
    {
        $amount = $this->countUntil(function ($val) use ($value) {
            return $val === $value;
        });

        return $this->skip($amount);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function until($value): self
    {
        $amount = $this->countUntil(function ($val) use ($value) {
            return $val === $value;
        });

        return $this->take($amount + 1);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function before($value): self
    {
        return $this->until($value)->take(-1);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function after($value): self
    {
        return $this->from($value)->skip(1);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function keyOf($value)
    {
        return array_search($value, $this->values);
    }

    /**
     * @param $value
     *
     * @return Iterator
     */
    public function keysOf($value): self
    {
        return new self(array_keys($this->values, $value));
    }

    /**
     * @param callable $callback
     *
     * @return bool
     */
    public function all(callable $callback): bool
    {
        foreach ($this->values as $key => $value) {
            if (!$callback($value, $key)) {
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
        foreach ($this->values as $key => $value) {
            if ($callback($value, $key)) {
                return true;
            }
        }

        return false;
    }
}
