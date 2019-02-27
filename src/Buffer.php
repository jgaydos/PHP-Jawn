<?php

namespace Jawn;

/**
 * Output buffering as I can never remember ob_yadda_yadda
 */
class Buffer
{
    private static $_buffer = null; // will be cleared upon calling on()

    /**
     * Turn on output buffering
     * @return  void
     */
    public static function on(): void
    {
        self::$_buffer = null;
        ob_start();
    }

    /**
     * Get current buffer contents and delete current output buffer
     * @return  string  Buffered output
     */
    public static function get(): string
    {
        if (ob_get_contents() !== false) {
            return ob_get_contents();
        }
        return self::$_buffer;
    }

    /**
     * Clean (erase) the output buffer and turn off output buffering
     * @return  string  Buffered output
     */
    public static function off(): string
    {
        self::$_buffer = ob_get_clean();
        return self::$_buffer;
    }
}
