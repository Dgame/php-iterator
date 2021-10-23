<?php

declare(strict_types=1);

namespace Dgame\Iterator;

use Safe\Exceptions\PcreException;

/**
 * @param iterable<int|string, mixed> $data
 *
 * @return Iterator<int|string, mixed>
 */
function iter(iterable $data): Iterator
{
    return new Iterator(...$data);
}

/**
 * @param string $str
 *
 * @return Iterator<int, string>
 */
function chars(string $str): Iterator
{
    return new Iterator(str_split($str, 1));
}

/**
 * @param string           $str
 * @param non-empty-string $delimiter
 *
 * @return Iterator<int, string>
 */
function separate(string $str, string $delimiter): Iterator
{
    return new Iterator(explode($delimiter, $str));
}

/**
 * @param string $str
 *
 * @return Iterator<int, string>
 * @throws PcreException
 */
function lines(string $str): Iterator
{
    $iter = new Iterator(\Safe\preg_split("/\r\n|\n|\r/", $str));

    return $iter->map('trim')->filter();
}

/**
 * @template V
 *
 * @param V $value
 *
 * @return Iterator<int|string, V>
 */
function only(mixed $value): Iterator
{
    return new Iterator(is_array($value) ? $value : [$value]);
}

/**
 * @template K of int|string
 *
 * @param array<K, mixed> $values
 *
 * @return Iterator<int, K>
 */
function keys(array $values): Iterator
{
    return new Iterator(array_keys($values));
}
