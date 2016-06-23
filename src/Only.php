<?php

namespace Dgame\Iterator;

/**
 * Class Only
 * @package Dgame\Iterator
 */
final class Only
{
    /**
     * @var mixed
     */
    private $value = null;

    /**
     * Only constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param int $n
     *
     * @return Iterator
     */
    public function repeat(int $n) : Iterator
    {
        return new Iterator(array_fill(0, $n, $this->value));
    }
}

/**
 * @param $value
 *
 * @return Only
 */
function only($value) : Only
{
    return new Only($value);
}