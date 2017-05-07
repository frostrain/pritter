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

    protected function bigIntToString($int)
    {
        // 不为 null 才转换
        return $int ? number_format($int, 0, '.', '') : $int;
    }

    /**
     * 在对象内保存为字符串的 字段数组.
     * 注意, 数据库里面保存的数据还是 big int 型.
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

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getBigIntFields())) {
            $value = $this->bigIntToString($value);
        }
        return parent::setAttribute($key, $value);
    }
}
