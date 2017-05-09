<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * 当 php 不支持 64 bit 时, bigInt 会被转换为 float , 这会造成精度问题.
 */
class BigIntTest extends TestCase
{
    /**
     * php 是 32bit 时, 会失败
     */
    public function testBigToStringConvert()
    {
        $str = '861211878055321602';
        $big = 861211878055321602;
        $str2 = '861211878055321601';
        $big2 = 861211878055321601;

        // 如果 php 是 32bit, 返回值已经溢出了
        $r = sprintf('%d', $big);
        $this->assertEquals($str, $r);
    }

    /**
     * php 是 32bit 时, 会失败
     */
    public function testBigIntCompare()
    {
        $big = 861211878055321602;
        $big2 = 861211878055321601;
        // 32bit 时, bigInt 被自动转换为了 float, 并且因为精度问题会认为这两个值相等
        $this->assertGreaterThan($big, $big2);
    }

    /**
     * php 是 32bit 时, 会失败
     */
    public function testBigIntStringToIntCompare()
    {
        $str = '861211878055321602';
        $str2 = '861211878055321601';
        // 如果是 32bit php, 两个值转换为 int 后变成了 2147483647(32bit 的最大值)
        $this->assertGreaterThan((int)$str2, intval($str));
    }

    /**
     * 这个例子竟然可以成功?
     */
    public function testBigIntStringCompare()
    {
        $str = '861211878055321601';
        $str2 = '861211878055321602';
        // GreaterThan 会将字符串转换数值貌似... 所以 32bit 会失败
        // $this->assertGreaterThan($str2, $str);
        $this->assertTrue($str2 > $str);
        $this->assertTrue($str > PHP_INT_MAX);
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
