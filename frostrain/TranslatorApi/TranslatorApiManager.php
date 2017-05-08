<?php

namespace Frostrain\TranslatorApi;

use Illuminate\Support\Manager;
use Frostrain\TranslatorApi\GoogleTranslator;

class TranslatorApiManager extends Manager
{
    /**
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'google';
    }

    public function createGoogleDriver()
    {
        return new GoogleTranslator();
    }
}