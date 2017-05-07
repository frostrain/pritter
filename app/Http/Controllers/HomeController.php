<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use App\Jobs\ParseTweetResponse;
use App\Jobs\GetTweetsTranslation;
use App\Models\Tweet;

class HomeController extends Controller
{
    public function index()
    {


        $file = 'homeTimeline_1494069757.json';
        $json = Storage::disk('public')->get($file);

        $lists = json_decode($json);



        // debug($lists[185]);

        return view('home.index', compact('lists'));
    }

    public function lists()
    {
        $lists = Tweet::paginate(100);
        return view('home.list', compact('lists'));
    }

    public function handle()
    {

        $file = 'homeTimeline_1494069757.json';
        $json = Storage::disk('public')->get($file);

        $data = json_decode($json, true);

        dispatch(new ParseTweetResponse($data));

        return view('home.list', ['lists' => []]);
    }

    public function translation()
    {
        $lists = Tweet::paginate(20);
        $ids = $lists->getCollection()->pluck('id')->toArray();

        dispatch(new GetTweetsTranslation($ids));

        return view('home.list', ['lists' => []]);
    }
}
