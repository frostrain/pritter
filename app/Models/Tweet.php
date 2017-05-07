<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TwitterTrait;
use App\Models\TwitterUser;
use App\Models\TwitterMedia;
use App\Models\Media;

class Tweet extends Model
{
    use TwitterTrait;

    public $incrementing = false;

    protected $fillable = [
        'id', 'retweeted_status', 'quoted_status', 'in_reply_to_status_id', 'in_reply_to_screen_name', 'text',
        'entities', 'user', 'retweet_count', 'favorite_count', 'lang', 'created_at',
        'extended_entities',
    ];

    /**
     * @var array
     */
    protected $entities;

    /**
     * 对象内保存为字符串的字段数组.
     * @return array
     */
    public function getBigIntFields()
    {
        return ['id', 'in_reply_to_status_id', 'quoted_id', 'retweeted_id', 'twitter_user_id'];
    }

    /**
     * @return bool
     */
    public function hasImage()
    {

    }

    public function translations()
    {
        return $this->hasMany('App\Models\TweetTranslation', 'tweet_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\TwitterUser', 'twitter_user_id', 'id');
    }

    public function retweetedStatus()
    {
        return $this->belongsTo('App\Models\Tweet', 'retweeted_id', 'id');
    }

    public function quotedStatus()
    {
        return $this->hasOne('App\Models\Tweet', 'quoted_id', 'id');
    }

    /**
     * @return array
     */
    public function getEntitiesAttribute()
    {
        if ($this->attributes['entities']) {
            if (!$this->entities) {
                $this->entities = json_decode($this->attributes['entities'], true);
            }

            return $this->entities;
        } else {
            return [];
        }
    }

    /**
     * @pararm array $entities
     * @return void
     */
    public function setEntitiesAttribute($entities)
    {
        // media 是图片, 不在这里保存
        unset($entities['media']);
        $this->attributes['entities'] = json_encode($entities);
        $this->entities = $entities;
    }

    public function setExtendedEntitiesAttribute($entities)
    {
        foreach ($entities['media'] as &$e) {
            if (!TweetMedia::find($e['id'])) {
                $t = new TweetMedia($e);
                $t->save();
            }

            if($e['type'] == 'animated_gif'){
                var_dump(json_encode($e));
            }
        }
    }

    /**
     * @param array $userData
     */
    public function setUserAttribute($userData)
    {
        $this->twitter_user_id = $userData['id'];
        $user = TwitterUser::find($userData['id']);
        if (!$user) {
            $user = new TwitterUser($userData);
        } else {
            // 即使存在也可能需要更新
            $user->fill($userData);
        }
        // 更新或保存用户
        $user->save();
    }

    /**
     * 设置 引用 的推文.
     * @param array $quotedStatus
     */
    public function setQuotedStatusAttribute($quotedStatus)
    {
        $quoted_id = $quotedStatus['id'];

        // 如果引用的推文还未保存, 直接保存
        if (!self::find($quoted_id)) {
            $quoted = new self($quotedStatus);
            $quoted->save();
        }
        $this->quoted_id = $quoted_id;
    }

    /**
     * 设置 转推 的推文.
     * @param array $retweetedStatus
     */
    public function setRetweetedStatusAttribute($retweetedStatus)
    {
        $retweeted_id = $retweetedStatus['id'];

        // 如果转推的推文还未保存, 直接保存
        if (!self::find($retweeted_id)) {
            $retweeted = new self($retweetedStatus);
            $retweeted->save();
        }
        $this->retweeted_id = $retweeted_id;
    }
}
