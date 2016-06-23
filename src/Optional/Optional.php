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
    abstract public function assume();

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

function maybe($value) : Optional
{
    if (Some::Verify($value)) {
        return some($value);
    }

    return none();
}