<?php

namespace Jawn\Io;

/**
 * You put it in and take it out
 */
class Csv
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
        $length = $options['length'] ?? 0;
        $delimiter = $options['delimiter'] ?? ',';
        $enclosure = $options['enclosure'] ?? '"';
        $escape_char = $options['escape_char'] ?? "\\";

        $ofTheKing = [];
        $columns = [];
        $row = 0;
        if (($handle = fopen($path, "r")) !== FALSE) {
            while (
                ($data = fgetcsv(
                    $handle,
                    $length,
                    $delimiter,
                    $enclosure,
                    $escape_char)
                ) !== FALSE
            ) {
                ++$row;
                for ($i = 0; $i < count($data); ++$i) {
                    if ($header && $row === 1) {
                        $columns[] = $data[$i];
                        continue;
                    }
                    $line[$columns[$i] ?? $i] = $data[$i];
                }
                if ($line ?? null !== null) {
                    $ofTheKing[] = $line;
                }
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
        $header = $options['header'] ?? true;
        $delimiter = $options['delimiter'] ?? ',';
        $enclosure = $options['enclosure'] ?? '"';
        $escape_char = $options['escape_char'] ?? "\\";

        $fp = fopen($path, 'w');
        foreach ($data as $fields) {
            if ($header === true) {
                $columns = [];
                foreach ($fields as $key => $crap) {
                    $columns[] = $key;
                }
                fputcsv($fp, $columns, $delimiter, $enclosure, $escape_char);
                $header = false;
            }
            fputcsv($fp, $fields, $delimiter, $enclosure, $escape_char);
        }
        fclose($fp);
    }
}
