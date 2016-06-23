<?php

namespace Dgame\Iterator\Optional;

/**
 * Class Optional
 * @package Dgame\Iterator\Optional
 */
abstract class Optional
{
    /**
     * @param $value
     *
     * @return Optional
     */
    public static function Identify($value) : Optional
    {
        if ($value !== null) {
            return some($value);
        }

        return none();
    }

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
    abstract public function maybe();

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

/**
 * @param $value
 *
 * @return Maybe
 */
function maybe($value) : Maybe
{
    return new Maybe($value);
}