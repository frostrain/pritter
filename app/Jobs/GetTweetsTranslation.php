<?php

namespace App\Jobs;

use App\Models\Tweet;
use App\Models\TweetTranslation;
// use Frostrain\GoogleTranslate\TranslateClient;
use Stichoza\GoogleTranslate\TranslateClient;

class GetTweetsTranslation
{
    /**
     * 需要处理的 tweet 的 id 数组
     * @var string
     */
    protected $ids;
    /**
     * Create a new job instance.
     * @pararm array $data
     * @return void
     */
    public function __construct($targets)
    {
        $this->ids = $targets;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->ids as $id) {
            $this->handleTweetTranslations($id);
        }
        // todo: max_id 等
    }

    protected function handleTweetTranslations($id)
    {
        // 如果已经存在, 直接返回
        if ($tweet = Tweet::find($id)) {
            $trans = $tweet->translations;
            if ($trans->count() == 0) {
                $this->requestGoogleTranslations($tweet);
            }
        }
    }

    protected function requestGoogleTranslations($tweet)
    {
        $tr_en = new TranslateClient();
        $tr_en->setSource('ja');
        $tr_en->setTarget('en');
        //$tr_en->setUrlBase('http://translate.google.cn/translate_a/single');

        $tr_cn = new TranslateClient();
        $tr_cn->setSource('ja');
        $tr_cn->setTarget('zh');
        //$tr_cn->setUrlBase('http://translate.google.cn/translate_a/single');

        try{
            $data_en = [
                'tweet_id' => $tweet->id,
                'lang' => 'en',
                'source' => 'google.cn',
                'text' => $tr_en->translate($tweet->text),
            ];

            $data_cn = [
                'tweet_id' => $tweet->id,
                'lang' => 'zh',
                'source' => 'google.cn',
                'text' => $tr_cn->translate($tweet->text),
            ];

            (new TweetTranslation($data_en))->save();
            (new TweetTranslation($data_cn))->save();
        }catch(\Exception $e){
            // 不管他
            debug($tweet->text);
        }




    }
}
