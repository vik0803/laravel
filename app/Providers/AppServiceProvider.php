<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require app_path('Helpers/helpers.php');
        view()->share('slug', \Slug::getSlug());
        view()->share('slugs', \Slug::getSlugs());
        view()->share('jsCookies', isset($_COOKIE['jsCookies']) ? json_decode($_COOKIE['jsCookies'], true) : []);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Http\Middleware\StartSessionExtend'); // Unikat extended session
    }
}
