<?php

namespace Io;

class Fixedwidth
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
        $header = $options['header'] ?? true;
        $length = $options['length'] ?? 1024;
        $columns = $options['columns'] ?? [];

        $ofTheKing = [];
        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgets($handle, $length)) !== FALSE) {
                $temp = [];
                foreach ($columns as $name => $loc) {
                    $temp[$name] = substr($data, $loc[0], $loc[1]);
                }
                $ofTheKing[] = $temp;
            }
            fclose($handle);
        }
        return $ofTheKing;
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
        echo 'Not implimented'.PHP_EOL;
    }
}
