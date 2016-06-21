<?php

namespace Dgame\Iterator\Optional;

/**
 * Class Optional
 * @package Dgame\Iterator\Optional
 */
abstract class Optional
{
    /**
     * @return bool
     */
    public function isSome() : bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isNone() : bool
    {
        return false;
    }

    /**
     * @return mixed
     */
    abstract public function may();

    /**
     * @return mixed
     */
    abstract public function get();
}

/**
 * @param $value
 *
 * @return Some
 */
function some($value) : Some
{
    return new Some($value);
}

/**
 * @return None
 */
function none() : None
{
    return None::Instance();
}