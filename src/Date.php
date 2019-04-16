<?php

namespace Jawn;

/**
 * Dates are stupid. Lets make them stupid easy.
 */
class Date
{
    /**
     * Formatt a date/time with text
     */
    public static function format(
        string $format = 'year-mon-day_hour-min-sec'
    ): string {
        $format = strtolower($format);
        $replace = [
            'year'   => 'Y',
            'month'  => 'm',
            'mon'    => 'm',
            'day'    => 'd',
            'hour'   => 'H',
            'minute' => 'i',
            'min'    => 'i',
            'second' => 's',
            'sec'    => 's',
        ];
        foreach ($replace as $old => $new) {
            $format = str_replace($old, $new, $format);
        }
        return date($format);
    }

    /**
     * Returns current date formatted YYYY-MM-DD
     */
    public static function now(string $dash = '-'): string
    {
        $format = implode([
            'Y',
            $dash,
            'm',
            $dash,
            'd'
        ]);
        return date($format);
    }

    /**
     * Returns current date formatted YYYY-MM-DD_HH-MI-SS
     */
    public static function time(
        string $dash = '-',
        string $underscore = '_'
    ): string {
        $format = implode([
            'Y',
            $dash,
            'm',
            $dash,
            'd',
            $underscore,
            'H',
            $dash,
            'i',
            $dash,
            's'
        ]);
        return date($format);
    }
}
