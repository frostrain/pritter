<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TimelineRequest;

class RequestPastHomeTimeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:past-home-timeline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Request <fg=yellow>past</fg=yellow> twitter api home timeline';

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
        $max_id = TimelineRequest::getMaxId();
        $this->call('pri:home-timeline', [
            '-m' => $max_id
        ]);
    }
}
