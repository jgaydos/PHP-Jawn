<?php

use PHPUnit\Framework\TestCase;
use Jawn\Io;

class IoTest extends TestCase
{
    public function testExtract()
    {
        $tests = ['csv', 'xls', 'xlsx'];

        foreach ($tests as $type) {
            $data = Io::extract($type, __DIR__.'/data/sample.'.$type);

            $this->assertEquals(count($data), 5000);
            $this->assertEquals($data[4999]['Id'], 6125);
        }
    }

    public function testExtractLoad()
    {
        $tests = ['csv', 'xls', 'xlsx'];

        foreach ($tests as $type) {
            $data = Io::extract($type, __DIR__.'/data/sample.'.$type);
            Io::load($type, __DIR__.'/data/sample1.'.$type, $data);
            $data1 = Io::extract($type, __DIR__.'/data/sample1.'.$type);
            unlink(__DIR__.'/data/sample1.'.$type);

            $this->assertEquals($data, $data1);
        }
    }
}
