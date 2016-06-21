<?php

namespace Dgame\Iterator;

/**
 * Class Assoc
 * @package Dgame\Iterator
 */
final class Assoc
{
    /**
     * @var Iterator|null
     */
    private $keys = null;
    /**
     * @var Iterator|null
     */
    private $values = null;

    /**
     * Assoc constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->keys   = keys($data);
        $this->values = iter($data);
    }

    /**
     * @return Iterator
     */
    public function getKeys() : Iterator
    {
        return $this->keys;
    }

    /**
     * @return Iterator
     */
    public function getValues() : Iterator
    {
        return $this->values;
    }

    /**
     * @param Iterator $keys
     */
    public function setKeys(Iterator $keys)
    {
        $this->keys = $keys;
    }

    /**
     * @param Iterator $values
     */
    public function setValues(Iterator $values)
    {
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function combine() : array
    {
        $keys   = $this->keys->collect();
        $values = $this->values->collect();

        if ($this->keys->amount() !== $this->values->amount()) {
            $keys = array_intersect_key($keys, $values);
        }

        return array_combine($keys, $values);
    }
}

/**
 * @param array $data
 *
 * @return Assoc
 */
function assoc(array $data) : Assoc
{
    return new Assoc($data);
}