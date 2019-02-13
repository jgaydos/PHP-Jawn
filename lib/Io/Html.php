<?php

namespace Io;

/**
 * you put it in and take it out
 */
class Html
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
        echo 'Not implimented'.PHP_EOL;
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

        if ($header === true) {
            $columns = [];
            foreach ($data[0] ?? [] as $key => $value) {
                $columns[] = $key;
            }
            array_unshift($data, $columns);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($data, NULL, 'A1');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
        $writer->save($path);
    }
}
