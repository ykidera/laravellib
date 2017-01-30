<?php

namespace Ykidera\Laravellib;

use Illuminate\Support\Facades\Facade;

class InoutFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'inout';
    }
}
