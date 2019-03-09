<?php

namespace Jawn;

class Dumpster
{
    private static $_garbage = [];

    public static function set(string $handle, $data): void
    {
        self::$_garbage[$handle] = $data;
    }

    public static function get(string $handle)
    {
        if (!isset(self::$_garbage[$handle])) {
            throw new \DumpsterHandleException("{$handle} doesn't exist.");
        }
        return self::$_garbage[$handle];
    }
}
