<?php

namespace Jawn\Io;

/**
 * You put it in and take it out
 * Recomended as JSON is able to distinguish between null and 'null'
 */
class Json
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
        return json_decode(file_get_contents($path)) ?: [];
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
        file_put_contents($path, json_encode($data));
    }
}
