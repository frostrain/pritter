<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interfaces\Downloadable;
use App\Models\TwitterTrait;
use App\Models\MediaTrait;
use Storage;

class TweetMedia extends Model implements Downloadable
{
    use TwitterTrait;
    use MediaTrait;

    public $incrementing = false;

    protected static $mediaType = [
        'unknown' => 0,
        'photo' => 1,
        'animated_gif' => 2,
        'video' => 3,
    ];

    /**
     * 允许 mass assign 的字段.
     * 注意: is_handled, size, disk, path 需要手动设置.
     */
    protected $fillable = [
        'id', 'id_str', 'tweet_id', 'media_url', 'type',
    ];

    public function getDownloadUrl()
    {
        return $this->media_url;
    }

    /**
     * 获取用来 排序 的字段. 用于优先下载.
     * @return array
     */
    protected static function getPriorityOrderBy()
    {
        return ['field' => 'tweet_id', 'direction' => 'desc'];
    }
}
