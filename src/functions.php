<?php

namespace Dgame\Iterator;

/**
 * @param array $data
 *
 * @return Iterator
 */
function iter(array $data): Iterator
{
    return new Iterator($data);
}

/**
 * @param string      $str
 * @param string|null $delimiter
 *
 * @return Iterator
 */
function chars(string $str, string $delimiter = null): Iterator
{
    if ($delimiter !== null) {
        return new Iterator(explode($delimiter, $str));
    }

    return new Iterator(str_split($str, 1));
}

/**
 * @param $value
 *
 * @return Iterator
 */
function only($value): Iterator
{
    return new Iterator(is_array($value) ? $value : [$value]);
}