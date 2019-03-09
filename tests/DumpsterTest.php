<?php

use PHPUnit\Framework\TestCase;
use Jawn\Dumpster;

class DumpsterTest extends TestCase
{
    public function testSetAppendGetData()
    {
        $data0 = [['a' => 1]];
        Dumpster::set('a', $data0);

        $this->assertEquals($data0, Dumpster::get('a'));
    }
}
