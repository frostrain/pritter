<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RequestHomeTimeline;
use App\Console\Commands\RequestLatestHomeTimeline;
use App\Console\Commands\RequestPastHomeTimeline;
use App\Console\Commands\RequestRateLimit;
use App\Console\Commands\ImportHomeTimeline;
use App\Console\Commands\Download;
use App\Console\Commands\CheckStorageMediaFile;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RequestHomeTimeline::class,
        RequestLatestHomeTimeline::class,
        RequestPastHomeTimeline::class,
        RequestRateLimit::class,
        ImportHomeTimeline::class,
        Download::class,
        CheckStorageMediaFile::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
