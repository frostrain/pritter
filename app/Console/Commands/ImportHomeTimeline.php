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
    protected $signature = 'pri:import-home-timeline {--c|count=3} {--f|file=}';

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
        $file = $this->option('file');
        if (strstr($file, ',')) {
            $file = explode(',', $file);
        }

        // 如果 $file 存在(本地文件), 则直接导入文件
        if ($file) {
            $this->importFromFiles($file);
        } else {
            $this->importFromRequests($count);
        }
    }

    protected function importFromRequests($count)
    {
        $requests = TimelineRequest::getUnimportedRequest($count);
        foreach ($requests as $r) {
            $json = Stroage::disk($r->disk)->get($r->path);
            $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
            dispatch(new ParseTweetResponse($data));
        }
    }

    protected function importFromFiles($files)
    {
        if ($files && !is_array($files)) {
            $files = func_get_args();
        }
        if (!is_array($files)) {
            $this->error('--file 参数错误! 命令终止!');
            return;
        }
        foreach ($file as $f) {
            // TODO: 读取文件
            $data = [];
            dispatch(new ParseTweetResponse($data));
        }
    }
}
