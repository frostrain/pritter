<?php

namespace App\Jobs;

use App\Models\Tweet;

class ParseTweetResponse
{
    /**
     * @var array
     */
    protected $data;
    /**
     * @var int 当前任务中插入的新推文数(不包括其中的引用/转推)
     */
    protected $new;
    /**
     * Create a new job instance.
     * @pararm array $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->data as $tweet) {
            $this->handleTweet($tweet);
        }
        // todo: max_id 等
    }

    protected function handleTweet($tweetData)
    {
        $id = $tweetData['id'];
        // 如果已经存在, 直接返回
        if ($tweet = Tweet::find($id)) {
            $tweet->fill($tweetData);
        } else {
            $tweet = new Tweet($tweetData);
            $this->new++;
        }

        $tweet->save();
    }
}
