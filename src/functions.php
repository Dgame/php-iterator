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
 * @param string $str
 *
 * @return Iterator
 */
function chars(string $str): Iterator
{
    return new Iterator(str_split($str, 1));
}

/**
 * @param string $str
 * @param string $delimiter
 *
 * @return Iterator
 */
function separate(string $str, string $delimiter): Iterator
{
    return new Iterator(explode($delimiter, $str));
}

/**
 * @param string $str
 *
 * @return Iterator
 */
function lines(string $str): Iterator
{
    $iter = new Iterator(preg_split("/\r\n|\n|\r/", $str));

    return $iter->map('trim')->filter();
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