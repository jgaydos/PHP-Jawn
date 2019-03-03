<?php

use PHPUnit\Framework\TestCase;
use Jawn\Buffer;

class BufferTest extends TestCase
{
    public function testBuffer()
    {
        Buffer::on();
        echo 'a';
        $get = Buffer::get();
        echo 'b';
        $off = Buffer::off();

        $this->assertEquals('a', $get);
        $this->assertEquals('ab', $off);
    }
}
