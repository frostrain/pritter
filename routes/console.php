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

});

Artisan::command('pri:info', function () {
    $max = ini_get('max_execution_time');
    $this->info('max execution time: '.$max);
});