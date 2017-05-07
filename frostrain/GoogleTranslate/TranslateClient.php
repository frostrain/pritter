<?php

namespace Frostrain\GoogleTranslate;

use Stichoza\GoogleTranslate\TranslateClient as Base;

class TranslateClient extends Base
{
    /**
     * @var TranslateClient Because nobody cares about singletons
     */
    protected static $staticInstance;
    /**
     * @var \GuzzleHttp\Client HTTP Client
     */
    protected $httpClient;
    /**
     * @var string Source language - from where the string should be translated
     */
    protected $sourceLanguage;
    /**
     * @var string Target language - to which language string should be translated
     */
    protected $targetLanguage;
    /**
     * @var string|bool Last detected source language
     */
    protected static $lastDetectedSource;
    /**
     * @var string Google Translate URL base
     */
    protected $urlBase = 'http://translate.google.com/translate_a/single';
    /**
     * @var array Dynamic guzzleHTTP client options
     */
    protected $httpOptions = [];
    /**
     * @var array URL Parameters
     */
    protected $urlParams = [
        'client'   => 't',
        'hl'       => 'en',
        'dt'       => 't',
        'sl'       => null, // Source language
        'tl'       => null, // Target language
        'q'        => null, // String to translate
        'ie'       => 'UTF-8', // Input encoding
        'oe'       => 'UTF-8', // Output encoding
        'multires' => 1,
        'otf'      => 0,
        'pc'       => 1,
        'trs'      => 1,
        'ssel'     => 0,
        'tsel'     => 0,
        'kc'       => 1,
        'tk'       => null,
    ];
    /**
     * @var array Regex key-value patterns to replace on response data
     */
    protected $resultRegexes = [
        '/,+/'  => ',',
        '/\[,/' => '[',
    ];
    /**
     * @var TokenProviderInterface
     */
    protected $tokenProvider;
    /**
     * @var string Default token generator class name
     */
    protected $defaultTokenProvider = GoogleTokenGenerator::class;

    public function setUrlBase($url){
        $this->urlBase = $url;
    }
}
