<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TwitterTrait;
use App\Models\AttributesTrait;
use App\Models\TwitterUser;
use App\Models\TwitterMedia;
use App\Models\Media;

class Tweet extends Model
{
    use TwitterTrait;
    use AttributesTrait;

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

    public function getIncrementAttributes()
    {
        return ['retweet_count', 'favorite_count'];
    }

    public function getBooleanAttributes()
    {
        return ['is_following_author', 'truncated'];
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
        return $this->retweeted_id && $this->retweeted_status ? true : false;
    }

    /**
     * 当前推文是否有 引用 其它推文
     * @return bool
     */
    public function hasQuote()
    {
        return $this->quoted_id && $this->quoted_status ? true : false;
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

    public function getTextAttribute()
    {
        $text = str_replace(["\n"], ['<br/>'], $this->attributes['text']);
        return $text;
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

    /**
     * 处理返回的数据中的 extended_entities 字段.
     * 注意, 这个 $entities 应该是不能修改的. 所以我们只需要创建, 不用更新.
     * @param array $entities
     */
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

        $this->is_following_author = array_get($userData, 'following', false);

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
