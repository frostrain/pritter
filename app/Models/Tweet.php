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
        'id', 'id_str', 'retweeted_status', 'quoted_status', 'in_reply_to_status_id',
        'in_reply_to_screen_name', 'truncated', 'text',
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
        // TODO
    }

    /**
     * 设置 truncated 属性.
     * 将传入的 $val 转换为 0 或 1.
     * 虽然不转换 $val 也可以工作, 但是 $model->getDirty() 会认为有数据'脏了'.
     * @param mixed $val
     */
    public function setTruncatedAttribute($val)
    {
        $this->attributes['truncated'] = $val ? 1 : 0;
    }

    /**
     * 当前推文是否是对其它推文的 回复
     * @return bool
     */
    public function isReply()
    {
        return $this->in_reply_to_status_id ? true : false;
    }

    /**
     * 当前推文是否是对其它推文的 转推
     * @return bool
     */
    public function isRetweet()
    {
        return $this->retweeted_id ? true : false;
    }

    /**
     * 当前推文是否有 引用 其它推文
     * @return bool
     */
    public function hasQuote()
    {
        return $this->quoted_id ? true : false;
    }

    public function translations()
    {
        return $this->hasMany('App\Models\TweetTranslation', 'tweet_id', 'id');
    }

    public function media()
    {
        return $this->hasMany('App\Models\TweetMedia', 'tweet_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\TwitterUser', 'twitter_user_id', 'id');
    }

    /**
     * 被 转推 的推文
     */
    public function retweeted_status()
    {
        return $this->belongsTo('App\Models\Tweet', 'retweeted_id', 'id');
    }

    /**
     * 被 引用 的推文
     */
    public function quoted_status()
    {
        return $this->belongsTo('App\Models\Tweet', 'quoted_id', 'id');
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
        if (!$this->exists) {
            static::saved(function ($tweet) use (&$entities) {
                if ($tweet->id === $this->id) {
                    foreach ($entities['media'] as $e) {
                        if (!TweetMedia::find($e['id'])) {
                            $e['tweet_id'] = $tweet->id;
                            $tm = new TweetMedia($e);
                            $tm->save();
                        }
                    }
                }
            });
        } else {
            foreach ($entities['media'] as $e) {
                if (!TweetMedia::find($e['id'])) {
                    $e['tweet_id'] = $this->id;
                    $tm = new TweetMedia($e);
                    $tm->save();
                }
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
