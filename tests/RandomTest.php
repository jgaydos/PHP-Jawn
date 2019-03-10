<?php

use PHPUnit\Framework\TestCase;
use Jawn\Random;

class RandomTest extends TestCase
{
    public function testFloat()
    {
        $float = Random::float();
        $this->assertEquals(is_float($float), true);

        $float = Random::float(2, 1);
        $this->assertEquals(is_float($float), true);
    }

    public function testInt()
    {
        $int = Random::int();
        $this->assertEquals($int >= 0 ? true : false, true);
        $this->assertEquals(is_int($int), true);

        $int = Random::int(255, 255);
        $this->assertEquals($int, 255);
        $this->assertEquals(is_int($int), true);
    }

    public function testString()
    {
        $string = Random::string();
        $this->assertEquals(strlen($string) >= 0 && strlen($string) <= 10, true);
        $this->assertEquals(is_string($string), true);

        $string = Random::string(255, 255);
        $this->assertEquals(strlen($string), 255);
        $this->assertEquals(is_string($string), true);
    }

    public function testTable()
    {
        $table = Random::table();
        $this->assertEquals(count($table), 10);
        $this->assertEquals(count($table[0]), 3);
        $this->assertEquals(isset($table[9]['col3']), true);
        $this->assertEquals(is_string($table[9]['col1']), true);
        $this->assertEquals(is_int($table[9]['col2']), true);
        $this->assertEquals(is_float($table[9]['col3']), true);

        $table = Random::table([
            'colz1' => 'string:1,10',
            'colz2' => 'int:1,10',
        ], 2);
        $this->assertEquals(count($table), 2);
        $this->assertEquals(count($table[0]), 2);
        $this->assertEquals(isset($table[2]), false);
        $this->assertEquals(isset($table[1]['colz2']), true);
    }
}
