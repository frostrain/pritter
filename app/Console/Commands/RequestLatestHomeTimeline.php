<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TimelineRequest;

class RequestLatestHomeTimeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:latest-home-timeline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request <fg=yellow>latest</fg=yellow> twitter api home timeline';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $since_id = TimelineRequest::getSinceId();

        // 注意, call 的第一个参数是 命令名, 如果有命令参数, 必须通过第二个参数传入
        $this->call('pri:home-timeline', [
            '-s' => $since_id
        ]);
    }
}
