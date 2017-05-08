<?php

namespace Frostrain\TranslatorApi;

use Stichoza\GoogleTranslate\TranslateClient;
use ReflectionClass;
use Frostrain\TranslatorApi\TranslatorInterface;

class GoogleTranslator implements TranslatorInterface
{
    /**
     * @var \Stichoza\GoogleTranslate\TranslateClient
     */
    protected $client;
    /**
     * @var array 默认值.
     */
    protected $defaultOptions;

    public function __construct($defaultOptions = [])
    {
        // 内容语言, 如果为 null 则表示自动判断
        $this->defaultOptions['source'] = array_get($defaultOptions, 'source', null);
        // 目标语言
        $this->defaultOptions['target'] = array_get($defaultOptions, 'target', 'zh-CN');

        $tran = new TranslateClient();

        $reflectedClass = new ReflectionClass(TranslateClient::class);
        $prop = $reflectedClass->getProperty('urlBase');
        $prop->setAccessible(true);
        $prop->setValue($tran, 'http://translate.google.cn/translate_a/single');

        $this->client = $tran;
    }

    /**
     * @param string $content
     * @options array $options ['source' => null, 'target' => 'lang']
     * @return string
     * @throws \Exception
     */
    public function translate($content, $options = [])
    {
        $source = array_get($options, 'source', $this->defaultOptions['source']);
        $target = array_get($options, 'target', $this->defaultOptions['target']);

        $this->client->setSource($source);
        $this->client->setTarget($target);

        return $this->client->translate($content);
    }
}