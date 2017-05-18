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
            // 注意, 程序中不应当使用 env 函数, 而应该使用 config
            // 因为 config 缓存之后, env 函数不能获取正确值
            $options = [
                'proxy' => config('ttwitter.tmhOAuth.curl_proxy'),
                'verify' => config('ttwitter.tmhOAuth.curl_ssl_verifypeer'),
            ];

            return new Client($options);
        });
    }
}
