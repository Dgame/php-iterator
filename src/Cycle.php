<?php

namespace Dgame\Iterator;

/**
 * Class Cycle
 * @package Dgame\Iterator
 */
final class Cycle
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Cycle constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param int $n
     *
     * @return Iterator
     */
    public function take(int $n) : Iterator
    {
        $data = [];
        for ($i = 0; $i < $n; $i++) {
            $data[] = $this->data;
        }

        return chain(...$data);
    }
}

/**
 * @param array $data
 *
 * @return Cycle
 */
function cycle(array $data) : Cycle
{
    return new Cycle($data);
}