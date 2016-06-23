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
     * @param $value
     *
     * @return bool
     */
    public static function Verify($value) : bool
    {
        return $value !== null && $value !== false;
    }

    /**
     * Some constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        if (!self::Verify($value)) {
            throw new \Exception('That is not a valid value');
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
    public function assume()
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
        return sprintf('Some(%s)', var_export($this->value, true));
    }
}