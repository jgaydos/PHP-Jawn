<?php

use PHPUnit\Framework\TestCase;
use Jawn\ETL as ETL;

class ETLTest extends TestCase
{
    public function testETL()
    {
        $data = [['a' => 1]];
        $ETL = new ETL;
        $ETL->extract('array', $data)
            ->transform('SELECT * FROM morty', 'morty');
        $this->assertEquals($data, $ETL->load('array', 'morty'));
    }
}
