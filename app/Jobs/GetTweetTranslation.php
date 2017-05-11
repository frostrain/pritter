<?php

namespace App\Jobs;

use App\Models\Tweet;
use App\Models\TweetTranslation;
// use Frostrain\GoogleTranslate\TranslateClient;
use Stichoza\GoogleTranslate\TranslateClient;
use ReflectionClass;

class GetTweetTranslation
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
        // 要翻译成的语言
        $langs = ['en', 'zh-CN'];
        $tr = new TranslateClient();
        // $tr->setSource('ja');

        $reflectedClass = new ReflectionClass(TranslateClient::class);
        $prop = $reflectedClass->getProperty('urlBase');
        $prop->setAccessible(true);
        $prop->setValue($tr, 'http://translate.google.cn/translate_a/single');

        //$tr_en->setUrlBase('http://translate.google.cn/translate_a/single');

        foreach ($langs as $lang) {
            try{
                $tr->setTarget($lang);

                $data = [
                    'tweet_id' => $tweet->id,
                    'lang' => $lang,
                    'source' => 'google.cn',
                    'text' => $tr->translate($tweet->text),
                ];

                (new TweetTranslation($data))->save();

            }catch(\Exception $e){
                // 无视异常, 继续执行
                debug($tweet->text);
            }
        }


    }
}
