<?php

namespace Jawn;

/**
 * Basically this stores data in memory via a SQLite database.
 */
class Coffer
{
    use Traits\SqlParamsTrait;

    private static $_data = null;

    public static function set(array $data, string $handle = 'morty'): void
    {
        if (self::$_data === null) {
            self::$_data = new SQLite3(':memory:');
        }

        self::dropTable($handle);
        $columns = array_keys($data[key($data)]);
        self::createTable($handle, $columns);
        self::import($handle, $data);
    }

    public static function append(array $data, string $handle = 'morty'): void
    {
        if (self::$_data === null) {
            exit('No memories found!');
        }

        if (!self::tableExist($handle)) {
            exit('Memory not found!');
        }

        self::import($handle, $data);
    }

    public static function query(
        string $query,
        array $params = [],
        string $handle = 'morty'
    ): array {
        $query = self::params($query, $params);
        $results = self::$_data->query($query);
        if ($results === false) {
            exit();
        }

        $oftheKing = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $oftheKing[] = $row;
        }

        if ($handle !== null) {
            self::set($oftheKing, $handle);
        }

        return $oftheKing;
    }

    public static function get(string $handle = 'morty'): array
    {
        if (self::$_data === null) {
            exit();
        }

        $query = 'SELECT * FROM ' . $handle;
        $results = self::$_data->query($query);
        if ($results === false) {
            exit();
        }

        $oftheKing = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $oftheKing[] = $row;
        }
        return $oftheKing;
    }

    public static function destroy(string $handle = 'morty'): void
    {
        $sql = "DROP TABLE IF EXISTS [{$name}]";

        $results = self::$_data->query($sql);

    }

    private static function tableExist(string $name): bool
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='{$name}'";

        $results = self::$_data->query($sql);

        $oftheKing = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            return true;
        }
        return false;
    }

    private static function createTable(string $name, array $columns): void
    {
        $columnsString = '';
        foreach ($columns as $column) {
            $column = self::clean($column);
            $columnsString .= ",[$column]";
        }
        $query = "CREATE TABLE IF NOT EXISTS [{$name}] (".substr($columnsString, 1).')';
        self::$_data->exec($query);
    }

    private static function dropTable(string $name): void
    {
        $query = "DROP TABLE IF EXISTS [{$name}]";
        self::$_data->query($query);
    }

    private static function import(
        string $table,
        array $data,
        bool $errors = true
    ): void {
        if (!is_array($data[key($data)] ?? null)) {
            $data = [$data];
        }
        $sql = '';
        foreach ($data as $row) {
            $columns = '';
            $values = '';
            foreach ($row as $name => $value) {
                $name = self::clean($name);
                $columns .= "[{$name}],";
                if ($value instanceof \DateTime) {
                    $values .= "'".$value->format('Y-m-d H:i:s')."',";
                } elseif (strlen($value) === 0) {
                    $values .= 'null,';
                } elseif (is_numeric($value)) {
                    $values .= $value.',';
                } else {
                    $values .= "'".str_replace("'","''",$value)."',";
                }
            }

            $columnsStr = substr($columns,0,-1);
            $valuesStr = substr($values,0,-1);
            $sql .= "INSERT INTO [{$table}] ({$columnsStr}) VALUES ({$valuesStr});";
        }
        self::$_data->exec($sql);
    }

    private static function clean(string $name): string
    {
        $dec = [
            0 => 31,
            127 => 255,
        ];
        foreach ($dec as $start => $end) {
            for ($i = $start; $i <= $end; ++$i) {
                $name = str_replace(chr($i), '', $name);
            }
        }
        return $name;
    }
}
