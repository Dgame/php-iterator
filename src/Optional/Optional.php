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
    abstract public function unwrap();

    /**
     * @param callable $callback
     *
     * @return Optional
     */
    abstract public function ensure(callable $callback) : Optional;

    /**
     * @param callable          $callback
     * @param string|\Exception $exception
     *
     * @return Some
     * @throws \Exception
     */
    final public function enforce(callable $callback, $exception) : Some
    {
        $result = $this->ensure($callback);
        if ($result->isNone()) {
            if ($exception instanceof \Exception) {
                throw $exception;
            }

            throw new \Exception($exception);
        }

        return $result;
    }
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