<?php

use PHPUnit\Framework\TestCase;
use Jawn\Hash;

class HashTest extends TestCase
{
    public function testMd5String()
    {
        $this->assertEquals(Hash::md5('a'), '0cc175b9c0f1b6a831c399e269772661');
    }

    public function testMd5Array()
    {
        $this->assertEquals(Hash::md5(['a']), '4c96bbc0e2390918dd50ef8e7eaff6e2');
    }

    public function testSha1String()
    {
        $this->assertEquals(Hash::sha1('a'), '86f7e437faa5a7fce15d1ddcb9eaeaea377667b8');
    }

    public function testSha1Array()
    {
        $this->assertEquals(Hash::sha1(['a']), '6b38d42036dacdcf081b8d1d0ee0c97b407c56c6');
    }
}
