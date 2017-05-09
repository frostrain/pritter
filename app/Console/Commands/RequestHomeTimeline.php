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
     * 获取 home_timeline
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

        // 写入模型的字段
        $dataFields = [
            'disk', 'path', 'since_id', 'max_id', 'start_id', 'end_id', 'count',
            'is_success', 'is_covered', 'file_size', 'is_imported', 'error',
        ];

        try {
            $response = Twitter::getHomeTimeline($options);

            $dir = date('Y/m/d/');
            $file = 'home_timeline_'.date('H-i').'.json';
            $path = $dir.$file;
            $disk = 'local';
            // 即使返回的是空数组也将其写入文件...
            Storage::disk($disk)->put($path, $response);
            $file_size = Storage::disk($disk)->size($path);

            $tweets = json_decode($response, true, 512, JSON_BIGINT_AS_STRING);
            $count = count($tweets);
            if ($count > 0) {
                $start_id = $tweets[$count - 1]['id_str'];
                $end_id = $tweets[0]['id_str'];
            } else {
                $start_id = null;
                $end_id = null;
            }

            // 注意即使实际数据量有 200 条
            // twitter 返回的数据量很有可能小于200 (会自动删除一部分敏感内容等)
            // 所以(即使数据量足够大)也不能确定返回数据总是有 200 条
            // 这里我们设置一个比较保险的值($threshold)作为判断标准
            // 返回的数据量小于这个 阈值 基本就可以确定覆盖当前范围了
            $threshold = intval($targetCount * 0.9);

            if (is_null($since_id) && is_null($max_id)) {
                // since_id 和 max_id 都是 null 说明是第一次开始采集
                // 第一次获取的总是最新的数据(覆盖了请求的范围!)
                $is_covered = true;
            } elseif (is_null($since_id) && !is_null($max_id)) {
                // 请求 过去的推特 (pri:past-home-timeline)
                // 这种情况总是覆盖整个范围
                $is_covered = true;
                if ($count == 0) {
                    // TODO: 过去的 旧推 已经全部获取了!!!
                }
            } elseif (!is_null($since_id) && is_null($max_id) && $count <= $threshold) {
                // 进入这里说明是 请求最新的推 (pri:latest-home-timeline)
                $is_covered = true;
            } elseif (!is_null($since_id) && !is_null($max_id)
                      && $count <= $threshold) {
                // 请求指定范围的推, $since_id < id <= $max_id
                $is_covered = true;
            }else {
                // $is_covered 即使为 false, 也只是表示不能确定是否真的有遗漏数据..
                // 需要进一步检查
                $is_covered = false;
            }

            $is_success = true;
            $is_imported = false;

            $data = compact($dataFields);
            (new TimelineRequest($data))->save();

            $this->info("done: \n new count: $count \n storage: [$disk] $path");
        } catch (\Exception $e) {
            $is_success = false;
            $error = $e->getMessage();
            $data = compact($dataFields);
            (new TimelineRequest($data))->save();

            $this->error('failed: '.$error);
        }
    }
}
