<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use App\Jobs\ParseTweetResponse;
use App\Jobs\GetTweetsTranslation;
use App\Models\Tweet;
use TranslatorApi;

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

        dispatch(new ParseTweetResponse($data));

        return view('home.index', ['lists' => []]);
    }

    // 测试
    public function convert()
    {
        $file = $this->file;
        $json = Storage::disk('public')->get($file);

        $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

        $first = $data[0];
        $t = Tweet::find($first['id']);

        $t->fill($first);
        debug($t->id);
        debug($t->getKey());
        debug($t->getDirty());

        $t = Tweet::whereNotNull('quoted_id')->first();
        debug($t->quoted_id);


        return view('home.index', ['lists' => []]);
    }

    public function translation()
    {
        $r = TranslatorApi::translate('hello');
        var_dump($r);
        return view('home.index', ['lists' => []]);
    }
}
