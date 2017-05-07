<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TwitterTrait;

class TweetTranslation extends Model
{
    use TwitterTrait;

    protected $fillable = [
        'text', 'lang', 'source', 'tweet_id',
    ];

    /**
     * 对象内保存为字符串的字段数组.
     * @return array
     */
    public function getBigIntFields()
    {
        return ['tweet_id'];
    }
}
