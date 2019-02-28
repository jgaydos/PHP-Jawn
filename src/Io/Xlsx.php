<?php

namespace Jawn\Io;

/**
 * you put it in and take it out
 * continue this process over and over
 * and you have lots of input and output
 */
class Xlsx
{
    private static $_history = [];

    /**
     * Extract or read from source and store as an array
     *
     * @access  public
     * @param   string  $path   Source
     * @param   string  $options   Extract options
     * @return  array
     */
    public static function extract(string $path, array $options = []) : array
    {
        $header = $options['header'] ?? true;
        $sheet = $options['sheet'] ?? null;

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        if ($sheet !== null) {
            $worksheet = $spreadsheet->setActiveSheetIndexByName($sheet);
        } else {
            $worksheet = $spreadsheet->getActiveSheet();
        }

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
        $sheet = $options['sheet'] ?? null;

        if ($header === true) {
            $columns = [];
            foreach ($data[0] ?? [] as $key => $value) {
                $columns[] = $key;
            }
            array_unshift($data, $columns);
        }

        // Load file if exist else create new
        if (\File::exists($path)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        } else {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        }

        // Check if sheet name specified
        if ($sheet === null) {
            $sheet = 'Sheet1';
        }

        self::$_history[$path][] = $sheet;

        // Remove sheet if already exists
        if ($spreadsheet->getSheetByName($sheet) !== null) {
            $sheetIndex = $spreadsheet->getIndex(
                $spreadsheet->getSheetByName($sheet)
            );
            $spreadsheet->removeSheetByIndex($sheetIndex);
        }

        // Remove all sheets not in history including the default sheet "Worksheet"
        foreach ($spreadsheet->getSheetNames() as $s) {
            if (!in_array($s, self::$_history[$path])) {
                $sheetIndex = $spreadsheet->getIndex(
                    $spreadsheet->getSheetByName($s)
                );
                $spreadsheet->removeSheetByIndex($sheetIndex);
            }
        }

        // Add sheet and make it as the active sheet
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheet);
        $spreadsheet->addSheet($myWorkSheet);
        $spreadsheet->setActiveSheetIndexByName($sheet);

        // Fill sheet with data
        $spreadsheet->getActiveSheet()->fromArray($data, null, 'A1');

        // Write sheet(s) to file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($path);
    }
}
