<?php

use PHPUnit\Framework\TestCase;
use Jawn\Date;

class DateTest extends TestCase
{
    public function testFormat()
    {
        $this->assertEquals(date('Y-m-d_H-i-s'), Date::format());
        $this->assertEquals(
            date('Y-m-m-d_H-i-i-s-s'),
            Date::format('Year-MON-mOnth-DAY_hour-MINUTE-min-second-SEc')
        );
    }

    public function testNow()
    {
        $this->assertEquals(date('Y-m-d'), Date::now());
        $this->assertEquals(date('Y_m_d'), Date::now('_'));
    }

    public function testTime()
    {
        $this->assertEquals(date('Y-m-d_H-i-s'), Date::time());
        $this->assertEquals(date('Y_m_d-H_i_s'), Date::time('_', '-'));
    }
}
