<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use App\Models\TimelineRequest;
use App\Jobs\ParseTweetResponse;

class ImportHomeTimeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:import-home-timeline {--c|count=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import home timeline from file';

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
        $count = $this->option('count');
    }
}
