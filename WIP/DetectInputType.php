<?php

$types = [
    'test.xlsx',
    'test.xls',
    'test.csv',
    'test.txt',
    'test.sql',
    'SELECT 1 a FROM test',
    'test',
    [['a' => 1]]
];

foreach ($types as $t) {
    $type = 'unknown';
    if (is_array($t)) {
        $type = 'array';
    } elseif (is_string($t)) {
        if (file_exists($t)) {
            $t2 = explode('.', $t);
            switch (end($t2)) {
                case 'csv':
                case 'txt':
                    $type = 'csv';
                    break;
                case 'xlsx':
                    $type = 'xlsx';
                    break;
                case 'xls':
                    $type = 'xls';
                    break;
                case 'sql':
                    $type = 'sql';
                    break;
            }
        } elseif (strpos(strtolower($t), 'select') !== false) {
            $type = 'query';
        }
    }
    echo $type.PHP_EOL;
}
