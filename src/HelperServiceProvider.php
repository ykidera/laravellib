<?php

namespace Ykidera\Laravellib;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    protected $defer = true;
    
    public function register()
    {
        $this->app->singleton('helper', function ($app) {
            return new Helper();
        });
        $this->app->alias('helper', 'Ykidera\Laravellib\Helper');
    }
    public function provides()
    {
        return ['helper'];
    }
}
