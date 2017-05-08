<?php

namespace Frostrain\TranslatorApi;

use Frostrain\TranslatorApi\TranslatorApiManager;
use Illuminate\Support\ServiceProvider;

class TranslatorApiServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('translator-api', function ($app) {
            return new TranslatorApiManager($app);
        });
    }
}