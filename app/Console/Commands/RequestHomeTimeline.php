<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use Twitter;
use App\Models\TimelineRequest;

class RequestHomeTimeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:home-timeline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'request twitter home timeline';

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
        $this->getLastestHomeTimeline();
    }

    protected function getLastestHomeTimeline()
    {
        $options = ['count' => 200, 'format' => 'json'];
        $since_id = TimelineRequest::getSinceId();
        if ($since_id) {
            $options['since_id'] = $since_id;
        }

        $dir = date('Y/m/d/');
        $file = 'home_timeline_'.date('H-i').'.json';
        $path = $dir.$file;
        $disk = 'local';

        $r = Twitter::getHomeTimeline($options);
        Storage::disk($disk)->put($path, $r);

        $tweets = json_decode($r, true, 512, JSON_BIGINT_AS_STRING);
        $count = count($tweets);
        if ($count > 0) {
            $start_id = $tweets[$count - 1]['id_str'];
            $end_id = $tweets[0]['id_str'];
        } else {
            $start_id = null;
            $end_id = null;
        }

        if ($count < $options['count']) {
            $is_covered = true;
        } elseif (is_null($since_id)) {
            $is_covered = true;
        } else {
            $is_covered = false;
        }

        $is_success = true;

        $data = compact('disk', 'path', 'since_id', 'start_id', 'end_id',
                        'count', 'is_success', 'is_covered');
        (new TimelineRequest($data))->save();
    }
}
