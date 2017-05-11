<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TwitterUser;

class TimelineStatistic extends Model
{
    protected $fillable = [
        'twitter_user_id', 'min_id', 'max_id',
    ];

    protected static $requestType = [
        'home' => 1, // home 类型的记录应该只有一条!
        'user' => 2,
    ];

    public function setRequestTypeAttribute($type)
    {
        $val = array_get(self::$requestType, $type);
        if ($val) {
            $this->attributes['request_type'] = $val;
        } else {
            throw new \InvalidArgumentException("不合法的类型: $type");
        }
    }

    /**
     * @return bool
     */
    public function isMinEnded()
    {
        return $this->is_min_end ? true : false;
    }

    public function setMinEnded()
    {
        $this->is_min_end = true;
        $this->save();
    }

    public function updateCount()
    {
        // user 类型才统计
        if ($this->attributes['request_type'] == 2) {
            $this->count = $this->user->tweets->count();
            $this->save();
        }
    }

    public function user()
    {
        return $this->belongsTo(TwitterUser::class, 'twitter_user_id', 'id');
    }

    public function setMinIdAttribute($min_id)
    {
        $cur_min = array_get($this->attributes, 'min_id');
        // 如果数据库中的 min_id 更小, 则直接返回
        if ($cur_min && $cur_min < $min_id) {
            return;
        } else {
            $this->attributes['min_id'] = $min_id;
        }
    }

    public function setMaxIdAttribute($max_id)
    {
        $cur_max = array_get($this->attributes, 'max_id');
        // 如果数据库中的 max_id 更大, 则直接返回
        if ($cur_max && $cur_max > $max_id) {
            return;
        } else {
            $this->attributes['max_id'] = $max_id;
        }
    }

    public static function getHomeTimelineStatistic()
    {
        $home = self::where('type', 1)->first();
        if (!$home) {
            $home = new self();
            $home->request_type = 'home';
            $home->save();
        }
        return $home;
    }

    public static function getUserTimelineStatistic($user_id)
    {
        $user = self::where('twitter_user_id', $user_id)
              ->where('type', 2)->first();
        if (!$user) {
            $user = new self();
            $user->request_type = 'user';
            $user->save();
        }
        return $user;
    }
}
