<?php

namespace Ykidera\Laravellib;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    protected $defer = true;
    
    public function register()
    {
        $this->app->singleton('helper', function ($app) {
            return new HelperBuilder();
        });
        $this->app->singleton('command.helper.make', function () {
            return new HelperCommand();
        });
        $this->commands([
            'command.helper.make',
        ]);
        $this->app->alias('helper', 'Ykidera\Laravellib\HelperBuilder');
    }
    public function provides()
    {
        return [
            'helper',
            'command.helper.make',
        ];
    }
}
