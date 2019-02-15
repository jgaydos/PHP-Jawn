<?php

namespace Jawn\Io;

class Table
{
    /**
     * Extract or read from source and store as an array
     *
     * @access  public
     * @param   string  $path   Source
     * @param   string  $options   Extract options
     * @return  array
     */
    public static function extract(string $path, array $options = []): array
    {
        $table = $path;
        $params = $options['params'] ?? [];
        $db = \DB::connection($options['connection'] ?? '') ?? [];
        return $db->query("SELECT * FROM $table", $params) ?? [];
    }

    /**
     * Load or write array to destination
     *
     * @access  public
     * @param   string  $path   Destination
     * @param   array   $data   Data to save
     * @param   string  $options   Loas options
     * @return  array
     */
    public static function load(string $path, array $data, array $options = []): void
    {
        $table = $path;
        $keys = $options['keys'] ?? []; // db column => array index
        $update = $options['update'] ?? true;
        $errors = $options['errors'] ?? true;
        $db = \DB::connection($options['connection'] ?? '') ?? [];
        $db->import($table, $data, $errors, $keys, $update) ?? [];
    }
}
