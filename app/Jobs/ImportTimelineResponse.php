<?php

namespace App\Jobs;

use Storage;
use App\Models\Tweet;
use App\Models\TimelineRequest;

/**
 * 从 数组 或 TimelineRequest 中导入 timeline 的数据.
 * 生成 Tweet 及相关联的 TwitterUser/TwitterMedia/Media 模型, 写入数据库.
 */
class ImportTimelineResponse
{
    /**
     * @var array
     */
    protected $data;
    /**
     * @var \App\Models\TimelineRequest
     */
    protected $request;
    /**
     * @var int 当前任务中插入的新推文数(不包括其中的引用/转推)
     */
    protected $new;
    /**
     * Create a new job instance.
     * 可以只传入 $data 数组, 或者只传入一个 $request.
     * @param array $data
     * @param mixed $requestId TimelineRequest 对象或者其 id
     * @return void
     */
    public function __construct($data, $request = null)
    {
        if (is_array($data)) {
            $this->data = $data;
            return;
        } elseif ($request) {
            if (is_numeric($request)) {
                $request = TimelineRequest::findOrFail($request);
            }
            if ($request instanceof TimelineRequest) {
                if (!$request->is_imporeted) {
                    $this->request = $request;
                    $this->data = $request->getResponseData();
                } else {
                    $this->data = [];
                }
                return;
            }
        }

        $name = get_class($this);
        throw new \InvalidArgumentException('传给 ['.$name.'] 的参数不合法!');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->request) {
            $this->request->is_imported = true;
            $this->request->save();
        }
        foreach ($this->data as $tweet) {
            $this->handleTweet($tweet);
        }
    }

    protected function handleTweet($tweetData)
    {
        $id = $tweetData['id'];
        // 如果已经存在, 则进行 覆写 操作
        if ($tweet = Tweet::find($id)) {
            $tweet->fill($tweetData);
        } else {
            $tweet = new Tweet($tweetData);
            $this->new++;
        }

        $tweet->save();
    }
}
