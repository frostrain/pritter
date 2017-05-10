<?php

use Illuminate\Foundation\Inspiring;
// use Twitter;
use App\Jobs\GetTimelineRequestFromFile as G;

use App\Jobs\ImportTimelineResponse;

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
    $path = '2017/05/09/home_timeline_'.time().'_'.str_random(6).'.json';
    $r = G::getCreateTimeFromPath($path);
    var_dump((string)$r);

    $t = microtime(true);
    $t = sprintf('%.2f', $t);
    var_dump($t);
    // new ImportTimelineResponse(null, '1');
});

Artisan::command('pri:info', function () {
    $max = ini_get('max_execution_time');
    $this->info('max execution time: '.$max);
});