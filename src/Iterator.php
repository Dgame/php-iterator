<?php

declare(strict_types=1);

namespace Dgame\Iterator;

/**
 * @template K of int|string
 * @template V
 */
final class Iterator
{
    /**
     * @param array<K, V> $values
     */
    public function __construct(private array $values)
    {
    }

    public function implode(string $glue = ''): string
    {
        return implode($glue, $this->values);
    }

    /**
     * @return array<K, V>
     */
    public function collect(): array
    {
        return $this->values;
    }

    public function isEmpty(): bool
    {
        return $this->values === [];
    }

    public function isNotEmpty(): bool
    {
        return $this->values !== [];
    }

    public function length(): int
    {
        return count($this->values);
    }

    /**
     * @return Iterator<int, V>
     */
    public function values(): self
    {
        return new self(array_values($this->values));
    }

    /**
     * @return self<int, K>
     */
    public function keys(): self
    {
        return new self(array_keys($this->values));
    }

    /**
     * @return V|null
     */
    public function first(): mixed
    {
        $first = reset($this->values);

        return $first === false ? null : $first;
    }

    /**
     * @return V|null
     */
    public function last(): mixed
    {
        $last = end($this->values);

        return $last === false ? null : $last;
    }

    /**
     * @return V|null
     */
    public function next(): mixed
    {
        $next = next($this->values);

        return $next === false ? null : $next;
    }

    /**
     * @return V|null
     */
    public function previous(): mixed
    {
        $prev = prev($this->values);

        return $prev === false ? null : $prev;
    }

    /**
     * @return V|null
     */
    public function peek(): mixed
    {
        $value = $this->next();
        $this->previous();

        return $value;
    }

    /**
     * @return V|null
     */
    public function popBack(): mixed
    {
        $last = array_pop($this->values);

        return $last === false ? null : $last;
    }

    /**
     * @return V|null
     */
    public function popFront(): mixed
    {
        $first = array_shift($this->values);

        return $first === false ? null : $first;
    }

    /**
     * @param int $amount
     *
     * @return self<K, V>
     */
    public function take(int $amount): self
    {
        return new self(array_slice($this->values, 0, $amount));
    }

    /**
     * @param int $amount
     *
     * @return self<K, V>
     */
    public function skip(int $amount): self
    {
        return new self(array_slice($this->values, $amount));
    }

    /**
     * @param int $times
     *
     * @return self<K, V>
     */
    public function repeat(int $times): self
    {
        $values = [];
        for ($i = 0; $i < $times; $i++) {
            $values += $this->values;
        }

        return new self($values);
    }

    /**
     * @param int $from
     * @param int $too
     *
     * @return self<K, V>
     */
    public function slice(int $from, int $too): self
    {
        return new self(array_slice($this->values, $from, $too - $from));
    }

    /**
     * @param int $size
     *
     * @return self<int|string, V[]>
     */
    public function chunks(int $size): self
    {
        /** @phpstan-ignore-next-line */
        return new self(array_chunk($this->values, $size));
    }

    /**
     * @param callable(V):V $callback
     * @param V $initial
     *
     * @return V
     */
    public function fold(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->values, $callback, $initial);
    }

    /**
     * @param callable(V):bool|null $callback
     *
     * @return self<K, V>
     */
    public function filter(callable $callback = null): self
    {
        if ($callback === null) {
            return new self(array_filter($this->values));
        }

        return new self(array_filter($this->values, $callback));
    }

    /**
     * @param callable(V):V $callback
     *
     * @return self<K, V>
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->values));
    }

    /**
     * @return self<K, V>
     */
    public function unique(): self
    {
        return new self(array_unique($this->values));
    }

    /**
     * @return self<K, V>
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->values));
    }

    /**
     * @param callable(V, K):bool $callback
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
     * @param callable(V, K):bool $callback
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
     * @param callable(V):bool $callback
     *
     * @return self<K, V>
     */
    public function takeWhile(callable $callback): self
    {
        return $this->take($this->countWhile($callback));
    }

    /**
     * @param callable(V):bool $callback
     *
     * @return self<K, V>
     */
    public function skipWhile(callable $callback): self
    {
        return $this->skip($this->countWhile($callback));
    }

    /**
     * @param V $value
     *
     * @return self<K, V>
     */
    public function from(mixed $value): self
    {
        $amount = $this->countUntil(static fn($val) => $val === $value);

        return $this->skip($amount);
    }

    /**
     * @param V $value
     *
     * @return self<K, V>
     */
    public function until(mixed $value): self
    {
        $amount = $this->countUntil(static fn($val) => $val === $value);

        return $this->take($amount + 1);
    }

    /**
     * @param V $value
     *
     * @return self<K, V>
     */
    public function before(mixed $value): self
    {
        return $this->until($value)->take(-1);
    }

    /**
     * @param V $value
     *
     * @return self<K, V>
     */
    public function after(mixed $value): self
    {
        return $this->from($value)->skip(1);
    }

    /**
     * @param K $value
     *
     * @return string|int|null
     */
    public function keyOf(mixed $value): string|int|null
    {
        $result = array_search($value, $this->values, strict: true);

        return $result === false ? null : $result;
    }

    /**
     * @param K $value
     *
     * @return self<int, K>
     */
    public function keysOf(mixed $value): self
    {
        return new self(array_keys($this->values, $value, strict: true));
    }

    /**
     * @param callable(V, K):bool $callback
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
     * @param callable(V, K):bool $callback
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
