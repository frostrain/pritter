<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Schema;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // mysql 5.7.7 以前必须设置, 否则会出错
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('guzzle.twitter', function ($app) {
            $options = [
                'proxy' => env("TWITTER_CURL_PROXY", false),
                'verify' => env('TWITTER_CURL_SSL_VERIFY', true),
            ];

            return new Client($options);
        });
    }
}
