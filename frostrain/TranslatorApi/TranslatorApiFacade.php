<?php

namespace Frostrain\TranslatorApi;

use Illuminate\Support\Facades\Facade;

class TranslatorApiFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'translator-api';
    }
}