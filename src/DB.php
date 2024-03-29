<?php

namespace Jawn;

/**
 * Database class
 */
class DB
{
    private $_connection = '';

    public static function connection($connection = ''): object
    {
        return Basket::database($connection);
    }

    public static function conn($connection = ''): object
    {
        return Basket::database($connection);
    }

    public static function query(/*$name = '', $query, $params = []*/)
    {
        $args = func_get_args();

        if (is_array($args[1] ?? [])) {
            $name = '';
            $query = $args[0];
            $params = $args[1] ?? [];
        } else {
            $name = $args[0];
            $query = $args[1];
            $params = $args[2] ?? [];
        }

        return Basket::database($name)->query($query, $params) ?? [];
    }

    public static function first(/*$name = '', $query, $params = []*/)
    {
        $args = func_get_args();

        if (is_array($args[1] ?? [])) {
            $name = '';
            $query = $args[0];
            $params = $args[1] ?? [];
        } else {
            $name = $args[0];
            $query = $args[1];
            $params = $args[2] ?? [];
        }

        return Basket::database($name)->query($query, $params)[0] ?? [];
    }

    public static function count()
    {
        $args = func_get_args();

        if (is_array($args[1] ?? [])) {
            $name = '';
            $query = $args[0];
            $params = $args[1] ?? [];
        } else {
            $name = $args[0];
            $query = $args[1];
            $params = $args[2] ?? [];
        }

        return count(Basket::database($name)->query($query, $params) ?? []);
    }

    public static function import(string $table, array $data, array $keys = [])
    {
        $args = func_get_args();

        if (is_string($args[1])) {
            $name = $args[0];
            $table = $args[1];
            $data = $args[2] ?? [];
            $keys = $args[3] ?? [];
        } else {
            $name = '';
            $table = $args[0];
            $data = $args[1] ?? [];
            $keys = $args[2] ?? [];
        }

        Basket::database($name)->import($table, $data, $keys);
    }
}
