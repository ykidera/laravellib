<?php

namespace Ykidera\Laravellib;

use Illuminate\Support\ServiceProvider;

class InoutServiceProvider extends ServiceProvider
{
    protected $defer = true;
    
    public function register()
    {
        $this->app->singleton('inout', function ($app) {
            return new Inout();
        });
        $this->app->alias('inout', 'Ykidera\Laravellib\Inout');
    }
    public function provides()
    {
        return ['inout'];
    }
}