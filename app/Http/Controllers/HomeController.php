<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use App\Jobs\ImportTimelineResponse;
use App\Jobs\GetTweetsTranslation;
use App\Models\Tweet;
use TranslatorApi;
use App\Models\TimelineRequest;

class HomeController extends Controller
{
    protected $file = 'homeTimeline_1494243163.json';
    public function index()
    {
        $file = $this->file;
        $json = Storage::disk('public')->get($file);

        $lists = json_decode($json);

        return view('home.index', compact('lists'));
    }

    public function lists()
    {
        $lists = Tweet::with('user', 'media')->orderBy('id', 'desc')->paginate(50);
        return view('home.list', compact('lists'));
    }

    public function handle()
    {
        $file = $this->file;
        $json = Storage::disk('public')->get($file);

        $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

        dispatch(new ImportTimelineResponse($data));

        return view('home.index', ['lists' => []]);
    }

    // 测试
    public function convert()
    {
        // $file = $this->file;
        // $json = Storage::disk('public')->get($file);

        // $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

        // $first = $data[0];

        $max = TimelineRequest::getMaxId();
        var_dump($max);


        $t = Tweet::first();

        var_dump($t->id);
        var_dump($t->getKey());


        $t = Tweet::whereNotNull('quoted_id')->first();
        var_dump($t->quoted_id);
        var_dump($t->favorite_count);

        $max = \DB::table('tweets')->max('id');
        var_dump($max);
        $max = \DB::table('tweets')->max('favorite_count');
        var_dump($max);

        $t = \DB::table('tweets')->first();

        return view('home.index', ['lists' => []]);
    }

    public function translation()
    {
        $r = TranslatorApi::translate('hello');
        var_dump($r);
        return view('home.index', ['lists' => []]);
    }
}
