<?php

namespace Jawn;

/**
 * Database class
 */
class DB
{
    private $_connection = '';

    public function connection($connection = ''): object
    {
        return Basket::database($connection);
    }

    public function conn($connection = ''): object
    {
        return Basket::database($connection);
    }

    public function query(/*$name = '', $query, $params = []*/)
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

        $ofTheKing = [];
        if (preg_match('%^(select)%is', $query) > 0) {
            $ofTheKing = Basket::database($name)->query($query, $params);
        } else {
            Basket::database($name)->execute($query, $params);
        }
        return $ofTheKing;
    }

    public function import($data = [], $keys = [])
    {
        if (empty($data)) {
            throw new Exception('Import: $data is empty');
        }
        if (empty($this->_table)) {
            throw new Exception('Import: Table must be set before import');
        }

        foreach ($data as $item) {
            $columns = '[' . implode('], [', array_keys($item)) . ']';
            $values = implode(', ', array_map(function ($v) {
                return (is_string($v) ? "'" . str_replace("'", "''", $v) . "'" : (($v instanceof DateTime)
                    ? "'{$v->format('Y-m-d H:i:s')}'" : ((is_null($v)) ? "NULL" : $v)));
            }, $item));
            "INSERT INTO [$this->_table] ($columns) VALUES ($values);";
        }
    }
}
