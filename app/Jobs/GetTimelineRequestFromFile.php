<?php

namespace App\Jobs;

use Storage;
use App\Models\TimelineRequest;
use Carbon\Carbon;

/**
 * 通过文件来获取 TimelineRequest 的模型.
 * 如果文件对应的模型不存在, 则创建该模型之后再返回模型.
 */
class GetTimelineRequestFromFile
{
    protected $disk;
    protected $path;
    protected $queries;
    protected $checkExists;
    /**
     * @var array
     */
    protected static $default = [
        'start_id' => null,
        'end_id' => null,
        'is_covered' => false,
        'is_imported' => false,
    ];

    /**
     * Create a new job instance.
     * @param null|string $disk
     * @param string $path
     * @param array $quires
     * @param bool $checkExists
     */
    public function __construct($disk, $path, $queries = [], $checkExists = true)
    {
        $this->path = trim(str_replace('\\', '/', $path), '/');
        $this->disk = $disk ?: config('filesystems.default');
        $this->queries = $queries;
        $this->checkExists = $checkExists;
    }

    /**
     * Execute the job.
     *
     * @return \App\Models\TimelineRequest
     */
    public function handle()
    {
        if ($this->checkExists) {
            $exists = TimelineRequest::where('path', $this->path)
                    ->where('disk', $this->disk)->first();
            if ($exists) {
                return $exists;
            }
        }

        $data = array_merge($this->queries, self::$default);

        $jsonStr = Storage::disk($this->disk)->get($this->path);
        $data['file_size'] = Storage::disk($this->disk)->size($this->path);

        $tweets = json_decode($jsonStr, true, 512, JSON_BIGINT_AS_STRING);
        $return_count = count($tweets);
        $data['return_count'] = $return_count;
        if ($return_count > 0) {
            $data['start_id'] = $tweets[$return_count - 1]['id_str'];
            $data['end_id'] = $tweets[0]['id_str'];
        }

        // 分析请求结果是否覆盖了整个范围
        $data['is_covered'] = self::analyseIsCovered($data);

        $data['disk'] = $this->disk;
        $data['path'] = $this->path;

        $model = new TimelineRequest($data);
        $created_at = self::getCreateTimeFromPath($this->path);
        if ($created_at) {
            $model->created_at = $created_at;
        }
        // 只有 return_count > 0 时才保存. 否则会出现大量无意义的记录...
        if ($model->return_count > 0) {
            $model->save();
        }
        return $model;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function analyseIsCovered($data)
    {
        // 这个不存在一般说明是从文件中导入的..
        $requestedCount = array_get($data, 'count');
        if (!$requestedCount) {
            return true;
        }
        // 注意即使实际数据量有 200 条
        // twitter 返回的数据量很有可能小于200 (会自动删除一部分敏感内容等)
        // 所以(即使数据量足够大)也不能确定返回数据总是有 200 条
        // 这里我们设置一个比较保险的值($threshold)作为判断标准
        // 返回的数据量小于这个 阈值 基本就可以确定覆盖当前范围了
        $threshold = intval($requestedCount * 0.9);

        $return_count = array_get($data, 'return_count');
        $since_id = array_get($data, 'since_id') ?: null;
        $max_id = array_get($data, 'max_id') ?: null;


        if (is_null($since_id) && is_null($max_id)) {
            // since_id 和 max_id 都是 null 说明是第一次开始采集
            // 第一次获取的总是最新的数据(覆盖了请求的范围!)
            $is_covered = true;
        } elseif (is_null($since_id) && !is_null($max_id)) {
            // 请求 过去的推特 (pri:past-home-timeline)
            // 这种情况总是覆盖整个范围
            $is_covered = true;
            if ($return_count == 0) {
                // TODO: 过去的 旧推 已经全部获取了!!!
            }
        } elseif (!is_null($since_id) && is_null($max_id)
                  && $return_count <= $threshold) {
            // 进入这里说明是 请求最新的推 (pri:latest-home-timeline)
            $is_covered = true;
        } elseif (!is_null($since_id) && !is_null($max_id)
                  && $return_count <= $threshold) {
            // 请求指定范围的推, $since_id < id <= $max_id
            $is_covered = true;
        }else {
            // $is_covered 即使为 false, 也只是表示不能确定是否真的有遗漏数据..
            // 需要进一步检查
            $is_covered = false;
        }

        return $is_covered;
    }

    /**
     * 尝试从文件路径中匹配出 创建时间.
     * @param string $path 文件路径.
     * @return false|string 返回创建时间, 如果返回 false, 说明没有匹配出时间...
     */
    public static function getCreateTimeFromPath($path)
    {
        // $1 是时间戳, $2 是随机字符串
        if (preg_match('/_(\d+)\_(\w+?).json$/', $path, $matches)) {
            $time = $matches[1];
            return Carbon::createFromTimestamp($time);
        }

        return false;
    }
}
