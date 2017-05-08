<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TwitterTrait;

class TwitterUser extends Model
{
    use TwitterTrait;

    public $incrementing = false;

    protected $fillable = [
        'id', 'id_str', 'name', 'screen_name', 'location', 'description', 'url', 'statuses_count',
        'favourites_count', 'followers_count', 'friends_count', 'created_at',
        'profile_image_url', 'profile_banner_url', 'profile_background_image_url',
    ];

    public function getBigIntFields()
    {
        return ['id'];
    }

    public function getProfileImageUrlAttribute()
    {
        // TODO: 实现头像采集
        return '/img/default_user.gif';
    }

    public function setProfileImageUrlAttribute($url)
    {

    }

    public function setProfileBannerUrlAttribute($url)
    {

    }

    public function setProfileBackGroundImageUrlAttribute($url)
    {

    }
}
