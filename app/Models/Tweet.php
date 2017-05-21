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
    /**
     * @var array
     */
    protected $fillable = [
        'id', 'id_str', 'retweeted_status', 'quoted_status', 'in_reply_to_status_id',
        'in_reply_to_screen_name', 'truncated', 'text',
        'entities', 'user', 'retweet_count', 'favorite_count', 'lang', 'created_at',
        'extended_entities',
    ];

    /**
     * @var array
     */
    protected $entitiesArray;

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

    /**
     * 处理推文中的 url
     * @param string $text
     * @param array $urls
     * @return string
     */
    protected function handleTextUrls($text, $urls)
    {
        if (!$urls){
            return $text;
        }

        $short = array_pluck($urls, 'url');
        $full = array_pluck($urls, 'expanded_url');
        foreach ($full as $k => $f) {
            if (preg_match('/twitter.com\/.*?\/status\/\d+/', $f)) {
                // 如果是其他推文的链接, 可以考虑直接删除链接
                // 这种链接一般是 引用 了其他推文, 自动加上去的?
                $full[$k] = '';
            } else {
                $full[$k] = "<a href='$f' target='_blank'>{$short[$k]}</a>";
            }
        }
        $text = str_replace($short, $full, $text);
        return $text;
    }

    /**
     * 处理推文中的 media
     * @param string $text
     * @param array $media
     * @return string
     */
    protected function handleTextMedia($text, $media)
    {
        if (!$media){
            return $text;
        }

        $short = array_pluck($media, 'url');
        $type = array_pluck($media, 'type');
        $expanded = array_pluck($media, 'expanded_url');
        foreach ($expanded as $k => $href) {
            if ($type[$k] === 'photo') {
                // 直接去掉图片链接?
                $expanded[$k] = '';
            } else {
                $expanded[$k] = "<a href='$herf' target='_blank'>{$short[$k]}</a>";
            }
        }
        $text = str_replace($short, $expanded, $text);
        return $text;
    }

    /**
     * 处理推文中的 hashtag
     * @param string $text
     * @param array $hashtags
     * @return string
     */
    protected function handleTextHashtags($text, $hashtags)
    {
        if (!$hashtags){
            return $text;
        }

        $tags = array_pluck($hashtags, 'text');
        $search = [];
        $replace = [];
        foreach ($tags as $k => $t) {
            $search[] = '#'.$t;
            $href = twitter_url('h', $t);
            $replace[] = "<a href='$href' target='_blank'>#{$t}</a>";
        }
        $text = str_replace($search, $replace, $text);
        return $text;
    }

    /**
     * 处理推文中的 metion, 也就是@用户.
     * @param string $text
     * @param array $metions
     * @return string
     */
    protected function handleTextMentions($text, $mentions)
    {
        if (!$mentions){
            return $text;
        }

        $usernames = array_pluck($mentions, 'screen_name');
        $search = [];
        $replace = [];
        foreach ($usernames as $k => $name) {
            $search[] = '@'.$name;
            $href = twitter_url('u', $name);
            $replace[] = "<a href='$href' target='_blank'>@{$name}</a>";
        }
        $text = str_replace($search, $replace, $text);
        return $text;
    }

    /**
     * 返回推文文本内容(html格式).
     * @return string
     */
    public function getTextAttribute()
    {
        $text = $this->attributes['text'];
        $entities = $this->entities;

        // 注意, 被转的推里面的 entities.urls 貌似没有 status 类型的链接..
        $text = $this->handleTextUrls($text, array_get($entities, 'urls'));
        $text = $this->handleTextMedia($text, array_get($entities, 'media'));
        $text = $this->handleTextHashtags($text, array_get($entities, 'hashtags'));
        $text = $this->handleTextMentions($text, array_get($entities, 'user_mentions'));

        $text = str_replace(["\n"], ['<br/>'], $text);

        return $text;
    }

    /**
     * 返回 entities 数组.
     * 包括 hashtags, symbols, user_metions, urls 字段.
     * @return array
     */
    public function getEntitiesAttribute()
    {
        if ($this->attributes['entities']) {
            if (!$this->entitiesArray) {
                $this->entitiesArray = json_decode($this->attributes['entities'], true, 512, JSON_BIGINT_AS_STRING);
            }

            return $this->entitiesArray;
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
        // unset($entities['media']);
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
