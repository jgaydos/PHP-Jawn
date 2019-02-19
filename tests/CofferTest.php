<?php

use PHPUnit\Framework\TestCase;
use Jawn\Coffer as Coffer;

class CofferTest extends TestCase
{
    public function testSetAppendGetData()
    {
        $data0 = [['a' => 1]];
        $data1 = [['a' => 2]];
        $data2 = [['a' => 1],['a' => 2]];
        Coffer::set($data0);
        Coffer::append($data1);
        $this->assertEquals($data2, Coffer::get());
    }
}
