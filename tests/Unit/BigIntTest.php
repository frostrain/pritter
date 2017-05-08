<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * php 的 bigInt 存在精度问题, 并且是于 系统/硬件? 有关的.
 * 还好当前应用中对于 bigInt 的精度要求并不是很高..
 */
class BigIntTest extends TestCase
{
    /**
     * 由于 精度问题 , 这个测试可能无法通过....
     */
    public function testBigToStringConvert()
    {
        $str = '861211878055321602';
        $big = 861211878055321602;

        // 返回的结果可能是 '861211878055321600'
        $r = number_format($big, 0, '.', '');
        $this->assertEquals($r, $str);
    }

    public function testBigToStringConvert2()
    {
        $str = '861370063940501504';
        $big = 861370063940501504;

        $r = number_format($big, 0, '.', '');
        $this->assertEquals($r, $str);
    }

    public function testStringToBigConvert()
    {
        $str = '861211878055321602';
        $big = 861211878055321602;

        $this->assertEquals((float)$str, $big);
    }

    public function testStringToBigConvert2()
    {
        $str = '861370063940501504';
        $big = 861370063940501504;

        $this->assertEquals((float)$str, $big);
    }

    public function testEquals()
    {
        $big1 = 861370063940501504;
        $big2 = 861370063940501504;
        $this->assertEquals($big1, $big2);
    }
}
