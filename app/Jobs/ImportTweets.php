<?php

namespace App\Jobs;

use Storage;
use App\Models\Tweet;
use App\Interfaces\TweetCollection;

/**
 * 从 TweetCollection 中导入 tweets.
 * 生成 Tweet 及相关联的 TwitterUser/TwitterMedia/Media 模型, 写入数据库.
 */
class ImportTweets
{
    /**
     * @var \App\Interfaces\TweetCollection
     */
    protected $collection;
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
    public function __construct(TweetCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->collection->isImported()){
            $data = $this->collection->getTweets();
            foreach ($data as $tweet) {
                $this->handleTweet($tweet);
            }
            // 这里应该注意一下有可能发生多个线程同时(重复)导入的情况...
            $this->collection->setImport(true);
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
