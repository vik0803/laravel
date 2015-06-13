<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Locales;

class LocaleServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['locales'] = $this->app->share(function() { return new Locales(); });
    }
}
