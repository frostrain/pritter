<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use Twitter;
use App\Models\TimelineRequest;
use App\Jobs\GetTimelineRequestFromFile;

class RequestHomeTimeline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pri:home-timeline {--s|since_id=} {--m|max_id=} {--c|count=200}';

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
        $since_id = $this->option('since_id');
        $max_id = $this->option('max_id');
        $count = $this->option('count');

        $this->getHomeTimeline($since_id, $max_id, $count);
    }

    /**
     * 获取 home_timeline 的数据, 并写入文件.
     */
    protected function getHomeTimeline($since_id, $max_id, $targetCount)
    {
        $options = ['count' => $targetCount, 'format' => 'json'];
        // $since_id = TimelineRequest::getSinceId();
        if ($since_id) {
            $options['since_id'] = $since_id;
        }
        if ($max_id) {
            $options['max_id'] = $max_id;
        }

        try {
            $response = Twitter::getHomeTimeline($options);
            $dir = date('Y/m/d/');
            // 时间戳放在前面是为了好排序...
            $file = 'home_timeline_'.time().'_'.str_random(6).'.json';
            $path = $dir.$file;
            $disk = config('pritter.default_disk');
            // 即使返回的是空数组也将其写入文件...
            Storage::disk($disk)->put($path, $response);

            $job = new GetTimelineRequestFromFile($disk, $path, $options, false);
            $request = $job->handle();
            $this->info("done!");
            $this->info("new count: {$request->return_count}");
            $this->info("is covered: {$request->is_covered}");
            if ($request->return_count) {
                $this->info("id range: {$request->start_id} => {$request->end_id}");
            }
            $this->info("storage: [{$request->disk}] {$request->path}");
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->error("failed: \n".$error);
        }
    }
}
