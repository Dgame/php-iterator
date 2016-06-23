<?php

namespace Dgame\Iterator\Optional;

/**
 * Class None
 * @package Dgame\Iterator\Optional
 */
final class None extends Optional
{
    /**
     * @var None
     */
    private static $instance = null;

    /**
     * None constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return None
     */
    public static function Instance() : None
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return bool
     */
    public function isNone() : bool
    {
        return true;
    }

    /**
     * @return NullObject
     */
    public function assume()
    {
        return NullObject::Instance();
    }

    /**
     * @throws \Exception
     */
    public function get()
    {
        throw new \Exception('No value');
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return 'None';
    }
}