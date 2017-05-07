<?php

use Illuminate\Foundation\Inspiring;
// use Twitter;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');


Artisan::command('pri:pu', function () {
    // $r = env('TWITTER_CURL_SSL_VERIFYPEER', true);
    // $r = Twitter::getLists();
    $r = Twitter::getHomeTimeline(['count' => 200, 'format' => 'json']);
    // $r = json_decode($r, true);
    // $r = Twitter::getUserTimeline(['screen_name' => 'iRis_k_miyu', 'count' => 20, 'format' => 'json']);

    $now = time();
    Storage::disk('public')->put('homeTimeline_'.$now.'.json', $r);

    $this->info('done!');
    // $this->comment(Inspiring::quote());
    // var_dump($r[0]);
});

Artisan::command('pri:t', function () {
    $time = 'Sat May 06 11:08:56 +0000 2017';
    $t = new Carbon\Carbon($time);
    var_dump($t);
});