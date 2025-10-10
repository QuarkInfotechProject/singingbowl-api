<?php

namespace App\Providers;

use App\Helpers\BarcodeHelper;
use Illuminate\Support\ServiceProvider;

class BarcodeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('barcode.dns1d', function ($app) {
            return new BarcodeHelper();
        });
    }

    public function boot()
    {
        //
    }
}
