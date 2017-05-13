<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TimelineRequest;

class Iteration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:iteration {--m|media-count=20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1. Reqeuest api. 2. Import Data. 3. Download media.';

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
        $mediaCount = $this->option('media-count');

        $this->call('pri:latest-home-timeline');
        $this->call('pri:import-home-timeline');
        $this->call('pri:download', [
            'type' => ['media', 'tweet-media'],
            '-c' => $mediaCount,
        ]);
    }
}
