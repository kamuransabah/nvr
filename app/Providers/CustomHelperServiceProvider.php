<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\StatusHelper;
use App\Facades\Status;

class CustomHelperServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Helper dosyalarını yükle
        foreach (glob(app_path('Helpers/*.php')) as $filename) {
            require_once $filename;
        }

        // StatusHelper için global erişim tanımla
        $this->app->singleton('status', function () {
            return new StatusHelper;
        });

    }

    public function register()
    {
        //
    }
}
