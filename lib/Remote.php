<?php

/**
 * Remote class
 */
class Remote
{
    /**
     * Magic method :)
     */
    public static function __callStatic(string $name, array $argv)
    {
        $obj = new class
        {
            /**
             * Returns connection by name
             */
            public function connection(string $connection = ''): object
            {
                return Basket::remote($connection);
            }

            /**
             * I'm lazy and this is shorter than typing connection
             */
            public function conn(string $connection = ''): object
            {
                return Basket::remote($connection);
            }
        };

        if (count($argv) === 0) {
            return $obj->{$name}();
        } elseif (count($argv) === 1) {
            return $obj->{$name}($argv[0]);
        } elseif (count($argv) === 2) {
            return $obj->{$name}($argv[0], $argv[1]);
        } elseif (count($argv) === 3) {
            return $obj->{$name}($argv[0], $argv[1], $argv[2]);
        } else {
            throw new Exception('Param number not handled');
        }
    }
}
