<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DNS1D extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'barcode.dns1d';
    }
}
