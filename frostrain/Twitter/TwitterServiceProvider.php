<?php

namespace Frostrain\Twitter;

use Thujohn\Twitter\TwitterServiceProvider as Base;
use Thujohn\Twitter\Twitter;

class TwitterServiceProvider extends Base
{
    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Twitter::class, function ($app){
            $t = new Twitter($app['config'], $app['session.store']);
            $tmhOAuthConfig = config('ttwitter.tmhOAuth', []);
            debug($tmhOAuthConfig);
            $t->reconfig($tmhOAuthConfig);
            return $t;
        });
    }
}
