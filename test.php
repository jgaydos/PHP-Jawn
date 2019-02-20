<?php
require 'vendor/autoload.php';

Jawn\Register::exectionTimer();
Jawn\Register::logger();

$ETL = new Jawn\ETL;
$ETL->extract('array', [['a' => 1]]);

Jawn\Console::table(
    Jawn\Coffer::get()
);
