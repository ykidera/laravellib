<?php

namespace Ykidera\Laravellib;

use Illuminate\Support\ServiceProvider;

class InoutServiceProvider extends ServiceProvider
{
    protected $defer = true;
    
    public function register()
    {
        $this->app->singleton('inout', function ($app) {
            return new InoutBuilder();
        });
        $this->app->alias('inout', 'Ykidera\Laravellib\InoutBuilder');
    }
    public function provides()
    {
        return ['inout'];
    }
}