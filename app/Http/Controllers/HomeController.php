<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tweet;

class HomeController extends Controller
{
    public function index()
    {
        // 这里有大量的关联预载入, 最好使用 html 文件缓存
        $lists = Tweet::with(
            'user', 'user.profile_image', 'media', 'retweeted_status',
            'retweeted_status.media', 'retweeted_status.user', 'retweeted_status.user.profile_image',
            'quoted_status', 'quoted_status.media', 'quoted_status.user', 'quoted_status.user.profile_image'
        )->where('is_following_author', true)
               ->orderBy('id', 'desc')->paginate(50);


        // debug($lists[0]->entities);
        debug($lists[9]->text);
        debug($lists[9]->retweeted_status->entities);
        debug($lists[9]->entities);
        debug($lists[10]->entities);

        return view('home.index', compact('lists'));
    }
}