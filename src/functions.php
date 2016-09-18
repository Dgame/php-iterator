<?php

namespace Dgame\Iterator;

/**
 * @param array[] ...$args
 *
 * @return Iterator
 */
function chain(array ...$args): Iterator
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
    return new Iterator(str_split($str));
}

/**
 * @param array $data
 *
 * @return Cycle
 */
function cycle(array $data): Cycle
{
    return new Cycle($data);
}

/**
 * @param $value
 *
 * @return Only
 */
function only($value): Only
{
    return new Only($value);
}