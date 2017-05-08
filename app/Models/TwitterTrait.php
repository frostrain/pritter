<?php

namespace App\Models;

use Carbon\Carbon;

trait TwitterTrait
{
    /**
     * laravel 默认主键是一个自增的 int.
     * 但是 trait 中不可以重复定义 字段 (默认值不同的字段)
     */
    //  public $incrementing = false;

    /**
     * @param float $int 这里是 64bit 的 bigInt
     */
    protected function bigIntToString($int)
    {
        // 不为 null 才转换
        return is_float($int) ? number_format($int, 0, '.', '') : $int;
    }

    /**
     * 在对象内保存为字符串的 字段数组.
     * 注意, bigint 型的数据应当以 字符串 输入, float 可能存在精度问题,
     * 只有 字符串 才能完全精确, 并且数据库也能正常工作.
     * 如果是 json_decode 解析获取数据, 需要使用 JSON_BIGINT_AS_STRING 参数.
     * @return array
     */
    abstract public function getBigIntFields();

    /**
     * @param string
     */
    public function setCreatedAtAttribute($time)
    {
        // created_at 保留为 字符串
        $this->attributes['created_at'] = (new Carbon($time))->__toString();
    }

    /**
     * 将 bigInt 字段转换为 字符串 来保存在对象内(数据库的数据还是 bigInt).
     * 但是注意, bigInt 转换为 字符串 的时候存在精度问题, 有可能产生损失..
     * 不过这种损失倒是具有一致性, 可以再现...
     */
    // public function setAttribute($key, $value)
    // {
    //     if (in_array($key, $this->getBigIntFields())) {
    //         $value = $this->bigIntToString($value);
    //     }
    //     return parent::setAttribute($key, $value);
    // }


    /**
     * 另一种方式是将获取到的 id 字符串转换为 bigInt
     */
    // public function getAttributeValue($key)
    // {
    //     $val = parent::getAttributeValue($key);
    //     if (in_array($key, $this->getBigIntFields()) && is_string($val)) {
    //         debug($val);
    //         $val = (float)$val;
    //     }
    //     return $val;
    // }

}
