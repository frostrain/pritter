<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TwitterTrait;
use App\Models\AttributesTrait;
use App\Models\Media;
use App\Models\Tweet;

class TwitterUser extends Model
{
    use TwitterTrait;
    use AttributesTrait;

    public $incrementing = false;

    protected $fillable = [
        'id', 'id_str', 'name', 'screen_name', 'location', 'description', 'url',
        'following', 'statuses_count',
        'favourites_count', 'followers_count', 'friends_count', 'created_at',
        'profile_image_url', 'profile_banner_url', 'profile_background_image_url',
        'profile_background_tile',
    ];

    public function getIncrementAttributes()
    {
        // 这两个基本可以确定是不会减少的...
        return ['statuses_count', 'followers_count'];
    }

    public function getBooleanAttributes()
    {
        return ['following', 'profile_background_tile'];
    }

    public function tweets()
    {
        return $this->hasMany(Tweet::class, 'twitter_user_id', 'id');
    }

    public function profile_image()
    {
        $type = Media::getTypeValue('user_profile_image');
        return $this->hasOne(Media::class, 'owner_id', 'id')->where('type', $type);
    }

    /**
     * 返回用户头像的 url.
     * @return string
     */
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image && $this->profile_image->isAvailable()) {
            return $this->profile_image->url;
        }
        // 默认头像
        return '/img/default_user.gif';
    }

    public function setProfileImageUrlAttribute($url)
    {
        if (!$url) {
            return;
        }
        $data = [
            'origin_url' => $url,
            'type' => 'user_profile_image',
            'owner_id' => $this->id,
        ];

        if ($media = $this->profile_image){
            $media->fill($data);
        } else {
            $media = (new Media($data));
        }
        $media->save();
    }

    public function setProfileBannerUrlAttribute($url)
    {

    }

    public function setProfileBackGroundImageUrlAttribute($url)
    {

    }
}
