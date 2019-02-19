<?php

namespace Jawn;

/**
 * Generates cryptographically secure-ish pseudo-random data
 */
class Random
{
    /**
     * Generate random float
     * @param   int     $min
     * @param   int     $max
     * @return  float
     */
    public static function float(int $length = 10, int $decimal = 2): float
    {
        $left = '';
        $right = '';

        while (strlen($left) < ($length - $decimal)) {
            $left .= self::int(0, 9);
        }

        while (strlen($right) < $decimal) {
            $right .= self::int(0, 9);
        }

        return floatval("{$left}.{$right}");
    }

    /**
     * Generate random int
     * @param   int     $min
     * @param   int     $max
     * @return  int
     */
    public static function int(int $min = 0, int $max = PHP_INT_MAX): int
    {
        return random_int($min, $max);
    }

    /**
     * Generate random string
     * @param   int    $length
     * @param   string  $keyspace
     * @return  string
     */
    public static function string(
        int $min = 1,
        int $max = 10,
        string $keyspace = ''
    ): string {
        if ($keyspace === '') {
            $keyspace = implode(range("0 ", "z"));
        }
        $length = self::int($min, $max);
        $ofTheKing = '';
        while(strlen($ofTheKing) < $length) {
            $ofTheKing .= $keyspace[random_int(0, strlen($keyspace)-1)];
        }
        return $ofTheKing;
    }

    public static function table(
        array $columns = [
            'col1' => 'string:1,10',
            'col2' => 'int:1,10',
            'col3' => 'float:10,2',
        ],
        int $rows = 5
    ): array {
        $ofTheKing = [];
        for ($i = 0; $i < $rows; ++$i) {
            foreach ($columns as $name => $meta) {
                list($type, $range) = explode(':', $meta);
                list($min, $max) = explode(',', $range);
                $ofTheKing[$i][$name] = self::$type($min, $max);
            }
        }
        return $ofTheKing;
    }
}
