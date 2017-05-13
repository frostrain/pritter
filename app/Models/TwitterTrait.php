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
     * @param string
     */
    public function setCreatedAtAttribute($time)
    {
        $timezone = config('app.timezone');
        if (!($time instanceof Carbon)) {
            $time = new Carbon($time);
        }
        // twitter 返回的数据是 UTC+0, 我们需要设置为本地时间
        // 数据库中保存的时间也是本地时间
        $time->setTimezone($timezone);
        $this->attributes['created_at'] = $time;
    }
}
