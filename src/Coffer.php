<?php

namespace Jawn;

/**
 * Basically this stores data in memory via a SQLite database.
 * I could have used the Sqlite class in this project but I
 * wanted this to be self containe.
 */
class Coffer
{
    use Traits\SqlParamsTrait;

    /** SQLite3 object */
    private static $_data = null;

    /**
     * Set handle data
     * @param   array   $data     The data to be imported
     * @param   string  $handle   Table name
     * @return  void
     */
    public static function set(array $data, string $handle = 'morty'): void
    {
        if (self::$_data === null) {
            self::$_data = new \SQLite3(':memory:');
        }

        self::dropTable($handle);
        $columns = array_keys($data[key($data)]);
        self::createTable($handle, $columns);
        self::import($handle, $data);
    }

    /**
     * Append handle data
     * @param   array   $data     The data to be imported
     * @param   string  $handle   Table name
     * @return  void
     */
    public static function append(array $data, string $handle = 'morty'): void
    {
        if (self::$_data === null) {
            throw new \CofferEmptyException('Must first use set data.');
        }

        if (!self::tableExist($handle)) {
            throw new \CofferHandleException("{$handle} not found.");
        }

        self::import($handle, $data);
    }

    /**
     * Run custom query
     * @param   string   $query    SQL query
     * @param   array    $params   Query params
     * @param   string   $handle   Table name
     * @return  void
     */
    public static function query(
        string $query,
        array $params = [],
        string $handle = 'morty'
    ): array {
        $query = self::params($query, $params);
        $results = self::$_data->query($query);
        if (!$results) {
            throw new \CofferQueryException('Query failed.');
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

    /**
     * Return stored handle data
     * @param   string  $handle   Table name
     * @return  array
     */
    public static function get(string $handle = 'morty'): array
    {
        if (self::$_data === null) {
            throw new \CofferEmptyException('Must first use set data.');
        }

        if (!self::tableExist($handle)) {
            throw new \CofferHandleException("{$handle} not found.");
        }

        $query = "SELECT * FROM [{$handle}]";
        $results = self::$_data->query($query);
        if (!$results) {
            throw new \CofferQueryException('Query failed.');
        }

        $oftheKing = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $oftheKing[] = $row;
        }
        return $oftheKing;
    }

    /**
     * Destroy/Drop table
     * @param   string  $handle   Table name
     * @return  void
     */
    public static function destroy(string $handle = 'morty'): void
    {
        $sql = "DROP TABLE IF EXISTS [{$handle}]";

        $results = self::$_data->query($sql);
    }

    /**
     * Check if table exists
     * @param   string  $handle     Table name
     * @return  bool
     */
    private static function tableExist(string $handle): bool
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name='{$handle}'";

        $results = self::$_data->query($sql);

        $oftheKing = [];
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            return true;
        }
        return false;
    }

    /**
     * Create table
     * @param   string  $handle     Table name
     * @param   array   $columns    Array of column names
     * @return void
     */
    private static function createTable(string $handle, array $columns): void
    {
        $columnsString = '';
        foreach ($columns as $column) {
            $column = self::clean($column);
            $columnsString .= ",[$column]";
        }
        $query = "CREATE TABLE IF NOT EXISTS [{$handle}] (".substr($columnsString, 1).')';
        self::$_data->exec($query);
    }

    /**
     * Drop table
     * @param   string   $handle    Table name
     * @return void
     */
    private static function dropTable(string $handle): void
    {
        $query = "DROP TABLE IF EXISTS [{$handle}]";
        self::$_data->query($query);
    }

    /**
     * Import data into table
     * @param   string  $handle    Table name
     * @param   array   $data      The data to be imported
     * @return  void
     */
    private static function import(
        string $handle,
        array $data
    ): void {
        if (!is_array($data[key($data)] ?? null)) {
            $data = [$data];
        }

        $formatValue = function ($v) {
            return (is_string($v) ? "'" . str_replace("'", "''", $v) . "'"
                : (($v instanceof DateTime) ? "'{$v->format('Y-m-d H:i:s')}'"
                : ((is_null($v)) ? "NULL"
                : $v)));
        };

        $formatColumn = function ($c) {
            foreach ([0 => 31, 123 => 255] as $start => $end) {
                for ($i = $start; $i <= $end; ++$i) {
                    $c = str_replace(chr($i), '', $c);
                }
            }
            return $c;
        };

        $sql = '';
        foreach ($data as $row) {
            $columns = '';
            $values = '';
            foreach ($row as $name => $value) {
                $columns .= "[{$formatKey($name)}],";
                $values .= "{$formatValue($value)},";
            }

            $columnsStr = substr($columns,0,-1);
            $valuesStr = substr($values,0,-1);
            $sql .= "INSERT INTO [{$handle}] ({$columnsStr}) VALUES ({$valuesStr});";
        }
        self::$_data->exec($sql);
    }
}
