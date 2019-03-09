<?php

use PHPUnit\Framework\TestCase;
use Jawn\Thread;

class ThreadTest extends TestCase
{
    public function testThreads()
    {
        $thread = new Thread;
        $thread->callback(function($a){return $a;});
        $thread->add(function(){return 1;});
        $thread->add(function(){return 2;});
        $thread->add(function(){return 3;});
        $thread->add(function(){return 4;});
        $a = $thread->run();

        $this->assertEquals($a, [1, 2, 3, 4]);
    }
}
