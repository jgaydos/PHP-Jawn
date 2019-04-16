<?php

namespace Jawn;

class Dumpster
{
    private static $_garbage = [];

    /**
     * Set data
     * @param   string  $handle
     * @param   mixed  $data
     * @return  void
     */
    public static function set(string $handle, $data): void
    {
        self::$_garbage[$handle] = $data;
    }

    /**
     * Get data
     * @param   string  $handle
     * @return   mixed
     */
    public static function get(string $handle)
    {
        if (!isset(self::$_garbage[$handle])) {
            throw new \DumpsterHandleException("{$handle} doesn't exist.");
        }
        return self::$_garbage[$handle];
    }
}
