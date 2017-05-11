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
}
