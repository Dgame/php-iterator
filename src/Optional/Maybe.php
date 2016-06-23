<?php

namespace Dgame\Iterator\Optional;

/**
 * Class Maybe
 * @package Dgame\Iterator\Optional
 */
final class Maybe extends Optional
{
    /**
     * @var Optional
     */
    private $optional = null;

    /**
     * Maybe constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->optional = Optional::Identify($value);
    }

    /**
     * @return bool
     */
    public function isSome() : bool
    {
        return $this->optional->isSome();
    }

    /**
     * @return bool
     */
    public function isNone() : bool
    {
        return $this->optional->isNone();
    }

    /**
     * @return mixed
     */
    public function maybe()
    {
        return $this->optional->maybe();
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->optional->get();
    }
}