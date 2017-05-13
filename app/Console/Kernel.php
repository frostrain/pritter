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
use App\Console\Commands\Iteration;
use App\Models\ErrorLog;
use Carbon\Carbon;

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
        Iteration::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('reminders:send')
        //             ->hourly()
        //             ->between('7:00', '22:00');

        $date = Carbon::now()->toDateString();
        $out = 'storage/logs/'.$date.'.log';

        try {
            // 本地 5点到24点, 每2分钟执行一次
            $schedule->command('pri:iteration')
                ->cron('*/2 * * * * *')
                ->between('5:00', '24:00');
            // ->appendOutputTo($out); // 追加写入
            // 本地 0点到5点, 每5分钟执行一次
            $schedule->command('pri:iteration')
                ->cron('*/5 * * * * *')
                ->between('0:00', '5:00');
        } catch (\Exception $e) {
            ErrorLog::log($e, ['type' => 1]);
        }

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
