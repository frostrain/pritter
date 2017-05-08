<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TwitterTrait;

class TweetMedia extends Model
{
    use TwitterTrait;

    public $incrementing = false;

    protected $fillable = [
        'id', 'id_str', 'tweet_id', 'media_url', 'type',
    ];

    /**
     * 对象内保存为字符串的字段数组.
     * @return array
     */
    public function getBigIntFields()
    {
        return ['id'];
    }

    public function getUrlAttribute()
    {
        if (!$this->disk) {
            return '/img/default_img.gif';
        } else {
        }
    }

    /**
     * @param string $val
     */
    public function setTypeAttribute($val)
    {
        $arr = ['photo' => 1, 'animated_gif' => 2, 'video' => 3];
        if (!isset($arr[$val])) {
            // throw \Exception('未知的 media 类型: '.$val);
            // 未知时, 先设置为 0
            $value = 0;
        } else {
            $value = $arr[$val];
        }

        $this->attributes['type'] = $value;
    }
}
