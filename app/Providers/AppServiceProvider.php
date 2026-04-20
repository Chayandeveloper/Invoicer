<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend('clerk', function ($app) use ($socialite) {
            $config = $app['config']['services.clerk'];
            return $socialite->buildProvider(\App\Socialite\ClerkProvider::class, $config);
        });
    }
}
