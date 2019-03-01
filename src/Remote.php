<?php

namespace Jawn;

/**
 * Remote class
 */
class Remote
{
    /**
     * Returns connection by name
     * @param   string  $connection    Connection name
     * @return  object
     */
    public function connection(string $connection = ''): object
    {
        return Basket::remote($connection);
    }

    /**
     * I'm lazy and this is shorter than typing connection
     * @param   string  $connection    Connection name
     * @return  object
     */
    public function conn(string $connection = ''): object
    {
        return Basket::remote($connection);
    }
}
