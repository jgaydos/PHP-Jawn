<?php

namespace Jawn;

/**
 * Output buffering
 */
class Buffer
{
    private static $buffer = null; // will be cleared upon calling on()

    /**
     * Turn on output buffering
     */
    public static function on(): void
    {
        self::$buffer = null;
        ob_start();
    }

    /**
     * Get current buffer contents and delete current output buffer
     */
    public static function get(): string
    {
        if (ob_get_contents() !== false) {
            return ob_get_contents();
        }
        return self::$buffer;
    }

    /**
     * Clean (erase) the output buffer and turn off output buffering
     */
    public static function off(): string
    {
        self::$buffer = ob_get_clean();
        return self::$buffer;
    }
}
