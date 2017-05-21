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
    $url = 'https://twitter.com/i/web/status/863381727963631616';
    if (preg_match('/twitter.com\/.*?\/status\/\d+/', $url)) {
        $this->info('y');
    }
})->describe('Test cmd');

Artisan::command('pri:info', function () {
    $time = \Carbon\Carbon::now();
    $max = ini_get('max_execution_time');
    $this->info($time);
    $this->info('max execution time: '.$max);
})->describe('Display some env information ...');