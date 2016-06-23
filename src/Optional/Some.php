<?php

namespace Dgame\Iterator\Optional;

/**
 * Class Some
 * @package Dgame\Iterator\Optional
 */
final class Some extends Optional
{
    /**
     * @var mixed
     */
    private $value = null;

    /**
     * Some constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        if ($value === null) {
            throw new \Exception('That is not some value');
        }

        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function isSome() : bool
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function maybe()
    {
        return $this->get();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return sprintf('Some(%s)', $this->value);
    }
}