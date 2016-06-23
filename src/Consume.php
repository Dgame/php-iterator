<?php

namespace Dgame\Iterator;

use Dgame\Iterator\Optional\Optional;
use function Dgame\Iterator\Optional\maybe;
use function Dgame\Iterator\Optional\none;

/**
 * Class Consume
 * @package Dgame\Iterator
 */
final class Consume
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Range constructor.
     *
     * @param Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->data = $iterator->collect();
    }

    /**
     * @return Iterator
     */
    public function intoIterator() : Iterator
    {
        return iter($this->data);
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->data);
    }

    /**
     * @return Optional
     */
    public function front() : Optional
    {
        if ($this->isEmpty()) {
            return none();
        }

        return maybe(reset($this->data));
    }

    /**
     * @return Optional
     */
    public function back() : Optional
    {
        if ($this->isEmpty()) {
            return none();
        }

        return maybe(end($this->data));
    }

    /**
     * @return Optional
     */
    public function popFront() : Optional
    {
        if (!$this->isEmpty()) {
            $value = array_shift($this->data);

            return maybe($value);
        }

        return none();
    }

    /**
     * @return Optional
     */
    public function popBack() : Optional
    {
        if (!$this->isEmpty()) {
            $value = array_pop($this->data);

            return maybe($value);
        }

        return none();
    }
}