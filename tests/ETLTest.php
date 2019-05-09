<?php

use PHPUnit\Framework\TestCase;
use Jawn\ETL;

class ETLTest extends TestCase
{
    public function testETL()
    {
        $data = [['a' => 1]];
        $ETL = new ETL;
        $ETL->extract('array', $data, 'a')
            ->transform('SELECT * FROM a', 'morty');
        $this->assertEquals($data, $ETL->load('array', 'morty'));
    }
}
