<?php

namespace Jawn\Io;

/**
 * you put it in and take it out
 * continue this process over and over
 * and you have lots of input and output
 */
class Xls
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

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $ofTheKing = [];
        $num = 0;
        foreach ($rows as $row) {
            ++$num;
            for ($i = 0; $i < count($row); ++$i) {
                if ($header && $num === 1) {
                    $columns[] = $row[$i];
                    continue;
                }
                $line[$columns[$i] ?? $i] = $row[$i];
            }
            if ($line ?? null !== null) {
                $ofTheKing[] = $line;
            }
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

        if ($header === true) {
            $columns = [];
            foreach ($data[0] ?? [] as $key => $value) {
                $columns[] = $key;
            }
            array_unshift($data, $columns);
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($data, NULL, 'A1');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save($path);
    }
}
